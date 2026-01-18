<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Grave;
use App\Models\LedgerOrder;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $request->validate([
            'grave_id' => 'required|exists:graves,grave_id',
            'buyer_name' => 'required',
            'buyer_phone' => 'required',
        ]);

        // Check if grave is actually occupied (You usually buy stones for occupied graves)
        $grave = Grave::find($request->grave_id);
        if($grave->status !== 'occupied') {
            return back()->withErrors(['grave_id' => 'Error: This grave plot is currently listed as Empty/Reserved. You can only buy ledgers for Occupied plots.']);
        }

        LedgerOrder::create([
            'grave_id' => $request->grave_id,
            'ledger_id' => $request->ledger_id,
            'buyer_name' => $request->buyer_name,
            'buyer_phone' => $request->buyer_phone,
            'amount' => $request->amount,
            'transaction_date' => now(),
            'status' => 'Pending'
        ]);

        return redirect()->route('public.services.success');
    }
}