<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Grave;
use App\Models\LedgerOrder;
use App\Models\Deceased;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublicLedgerController extends Controller
{
    public function index()
    {
        $ledgers = Ledger::all();
        return view('public.services.catalog', compact('ledgers'));
    }

    public function create($id)
    {
        $ledger = Ledger::findOrFail($id);
        return view('public.services.order', compact('ledger'));
    }

    // CREATE BILL & REDIRECT
    public function store(Request $request)
    {
        $request->validate([
            'grave_id' => 'required|exists:graves,grave_id',
            'ledger_id' => 'required|exists:ledgers,ledger_id',
            'buyer_name' => 'required',
            'buyer_phone' => 'required',
            'amount' => 'required|numeric|min:1'
        ]);

        // ✅ Validate Grave Availability using ledger_id
        $grave = Grave::where('grave_id', $request->grave_id)
                      ->where('status', 'occupied')
                      ->whereNull('ledger_id')
                      ->first();

        if (!$grave) {
            return back()->withErrors([
                'grave_id' => 'This grave already has a ledger or is not available.'
            ])->withInput();
        }

        // Temporary reference
        $tempRef = 'TEMP-' . Str::random(10);
        $amountCents = $request->amount * 100;

        // ToyyibPay bill
        $billData = [
            'userSecretKey' => env('TOYYIBPAY_SECRET'),
            'categoryCode' => env('TOYYIBPAY_CATEGORY'),
            'billName' => 'Headstone Order',
            'billDescription' => 'Grave ID: ' . $request->grave_id,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $amountCents,
            'billReturnUrl' => route('public.ledgers.return'),
            'billCallbackUrl' => route('public.ledgers.callback'),
            'billExternalReferenceNo' => $tempRef,
            'billTo' => $request->buyer_name,
            'billEmail' => 'noreply@example.com',
            'billPhone' => $request->buyer_phone,
            'billPaymentChannel' => 'FPX',
            'billChargeToCustomer' => 1,
        ];

        $response = Http::asForm()->post(env('TOYYIBPAY_URL') . '/index.php/api/createBill', $billData);
        $billCode = $response->json()[0]['BillCode'] ?? null;

        if (!$billCode) {
            return back()->with('error', 'Payment gateway error. Please try again.');
        }

        Cache::put('temp_order_' . $billCode, [
            'grave_id' => $request->grave_id,
            'ledger_id' => $request->ledger_id,
            'buyer_name' => $request->buyer_name,
            'buyer_phone' => $request->buyer_phone,
            'amount' => $request->amount,
        ], 7200);

        return redirect(env('TOYYIBPAY_URL') . '/' . $billCode);
    }

    // PAYMENT RETURN
    public function paymentReturn(Request $request)
    {
        if ($request->status_id != 1) {
            return redirect()->route('public.services.index')
                ->with('error', 'Payment failed or cancelled.');
        }

        $billCode = $request->billcode;
        $data = Cache::get('temp_order_' . $billCode);

        if (!$data) {
            $existing = LedgerOrder::where('bill_code', $billCode)->first();
            if ($existing) {
                return redirect()->route('public.services.success')
                    ->with(['order_id' => $existing->order_id, 'amount' => $existing->amount]);
            }

            return redirect()->route('public.services.index')
                ->with('error', 'Session expired. Please contact admin.');
        }

        $order = LedgerOrder::firstOrCreate(
            ['bill_code' => $billCode],
            [
                'grave_id' => $data['grave_id'],
                'ledger_id' => $data['ledger_id'],
                'buyer_name' => $data['buyer_name'],
                'buyer_phone' => $data['buyer_phone'],
                'amount' => $data['amount'],
                'transaction_date' => now(),
                'status' => 'Pending'
            ]
        );

        // 🔒 Lock grave
        Grave::where('grave_id', $data['grave_id'])
             ->whereNull('ledger_id')
             ->update(['ledger_id' => $data['ledger_id']]);

        Cache::forget('temp_order_' . $billCode);

        return redirect()->route('public.services.success')
            ->with(['order_id' => $order->order_id, 'amount' => $order->amount]);
    }

    // PAYMENT CALLBACK
    public function paymentCallback(Request $request)
    {
        if ($request->status != 1) return;

        $billCode = $request->billcode;
        $data = Cache::get('temp_order_' . $billCode);

        if (!$data) return;

        LedgerOrder::firstOrCreate(
            ['bill_code' => $billCode],
            [
                'grave_id' => $data['grave_id'],
                'ledger_id' => $data['ledger_id'],
                'buyer_name' => $data['buyer_name'],
                'buyer_phone' => $data['buyer_phone'],
                'amount' => $data['amount'],
                'transaction_date' => now(),
                'status' => 'Pending'
            ]
        );

        Grave::where('grave_id', $data['grave_id'])
             ->whereNull('ledger_id')
             ->update(['ledger_id' => $data['ledger_id']]);

        Cache::forget('temp_order_' . $billCode);
    }

    // SEARCH DECEASED
    public function searchDeceased(Request $request)
    {
        $query = $request->get('query');
        if (strlen($query) < 1) return response()->json([]);

        $q = Deceased::whereHas('grave', function ($g) {
            $g->where('status', 'occupied')
              ->whereNull('ledger_id');
        });

        if (is_numeric($query)) {
            $q->where(function ($sub) use ($query) {
                $sub->where('grave_id', $query)
                    ->orWhere('full_name', 'like', "%{$query}%");
            });
        } else {
            $q->where('full_name', 'like', "%{$query}%");
        }

        return response()->json(
            $q->with('grave:grave_id,section_id')
              ->limit(5)
              ->get(['deceased_id', 'full_name', 'grave_id', 'date_of_death'])
        );
    }
}
