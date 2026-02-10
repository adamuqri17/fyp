<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Grave;
use App\Models\LedgerOrder;
use App\Models\Deceased;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache; // Import Cache
use Illuminate\Support\Str; // Import Str

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

    // 1. CREATE BILL & REDIRECT (DO NOT SAVE TO DB YET)
    public function store(Request $request)
    {
        $request->validate([
            'grave_id' => 'required|exists:graves,grave_id',
            'buyer_name' => 'required',
            'buyer_phone' => 'required',
        ]);

        // A. Validate Grave
        $grave = Grave::find($request->grave_id);
        if (!$grave || $grave->status !== 'occupied') {
            return back()->withErrors(['grave_id' => 'Invalid Selection: Plot is Empty/Reserved.'])->withInput();
        }

        // B. Check Existing Paid Orders
        $hasLedger = LedgerOrder::where('grave_id', $request->grave_id)
                                ->whereIn('status', ['Pending', 'Installed']) // Pending now means "Paid & Waiting Install"
                                ->exists();

        if ($hasLedger) {
            return back()->withErrors(['grave_id' => 'This grave already has a confirmed order.'])->withInput();
        }

        // C. Generate Temporary Reference (Since we don't have an Order ID yet)
        $tempRef = 'TEMP-' . Str::random(10);
        $amountCents = $request->amount * 100;

        // D. Call ToyyibPay
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
            'billSplitPayment' => 0,
            'billPaymentChannel' => 'FPX',
            'billContentEmail' => '',
            'billChargeToCustomer' => 1,
        ];

        $response = Http::asForm()->post(env('TOYYIBPAY_URL') . '/index.php/api/createBill', $billData);
        $billCode = $response->json()[0]['BillCode'] ?? null;

        if ($billCode) {
            // E. STORE DATA IN CACHE (Expires in 2 hours)
            // We use the BillCode as the key to retrieve this data later
            $orderData = [
                'grave_id' => $request->grave_id,
                'ledger_id' => $request->ledger_id,
                'buyer_name' => $request->buyer_name,
                'buyer_phone' => $request->buyer_phone,
                'amount' => $request->amount,
                'bill_code' => $billCode,
            ];

            Cache::put('temp_order_' . $billCode, $orderData, 7200); // 7200 seconds = 2 hours

            // Redirect to Payment
            return redirect(env('TOYYIBPAY_URL') . '/' . $billCode);
        } else {
            return back()->with('error', 'Payment gateway error. Please try again.');
        }
    }

    // 2. HANDLE RETURN (User redirected back)
    public function paymentReturn(Request $request)
    {
        $statusId = $request->status_id; // 1=Success
        $billCode = $request->billcode;

        if ($statusId == 1) {
            // A. Retrieve Data from Cache
            $data = Cache::get('temp_order_' . $billCode);

            if ($data) {
                // B. Create Order in DB (Only now!)
                // Use updateOrCreate to prevent duplicates if user refreshes page
                $order = LedgerOrder::firstOrCreate(
                    ['bill_code' => $billCode], // Check unique bill code
                    [
                        'grave_id' => $data['grave_id'],
                        'ledger_id' => $data['ledger_id'],
                        'buyer_name' => $data['buyer_name'],
                        'buyer_phone' => $data['buyer_phone'],
                        'amount' => $data['amount'],
                        'transaction_date' => now(),
                        'status' => 'Pending' // Pending = Installation Pending
                    ]
                );

                // C. Clear Cache
                Cache::forget('temp_order_' . $billCode);

                return redirect()->route('public.services.success')->with(['order_id' => $order->order_id, 'amount' => $order->amount]);
            } else {
                // Edge case: Data expired or already processed
                // Check if order exists in DB anyway
                $existingOrder = LedgerOrder::where('bill_code', $billCode)->first();
                if ($existingOrder) {
                    return redirect()->route('public.services.success')->with(['order_id' => $existingOrder->order_id, 'amount' => $existingOrder->amount]);
                }
                
                return redirect()->route('public.services.index')->with('error', 'Session expired. Please contact admin if payment was deducted.');
            }
        } else {
            return redirect()->route('public.services.index')->with('error', 'Payment failed or cancelled. No order was created.');
        }
    }

    // 3. CALLBACK (Server-to-Server)
    public function paymentCallback(Request $request)
    {
        $billCode = $request->billcode;
        $status = $request->status; // 1=Success

        if ($status == 1) {
            // Retrieve Data from Cache
            $data = Cache::get('temp_order_' . $billCode);

            if ($data) {
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
                Cache::forget('temp_order_' . $billCode);
            }
        }
    }

    // 4. SEARCH (Helper)
    public function searchDeceased(Request $request)
    {
        $query = $request->get('query');
        if(strlen($query) < 1) return response()->json([]);

        // Exclude graves that already have a Pending (Paid) or Installed order
        $occupiedGraveIds = LedgerOrder::whereIn('status', ['Pending', 'Installed'])
                                       ->pluck('grave_id')
                                       ->toArray();

        $q = Deceased::whereHas('grave', function($g) use ($occupiedGraveIds) {
            $g->where('status', 'occupied')
              ->whereNotIn('grave_id', $occupiedGraveIds);
        });

        if (is_numeric($query)) {
            $q->where(function($sub) use ($query) {
                $sub->where('grave_id', $query)->orWhere('full_name', 'like', "%{$query}%");
            });
        } else {
            $q->where('full_name', 'like', "%{$query}%");
        }

        $results = $q->with('grave:grave_id,section_id')->limit(5)->get(['deceased_id', 'full_name', 'grave_id', 'date_of_death']);
        return response()->json($results);
    }
}