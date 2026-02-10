<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class AdminLedgerController extends Controller
{
    public function index()
    {
        $ledgers = Ledger::all();
        return view('admin.ledgers.index', compact('ledgers'));
    }

    public function create()
    {
        return view('admin.ledgers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'material' => 'required',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = 'images/placeholder.jpg'; 

        if ($request->hasFile('picture')) {
            $imagePath = $request->file('picture')->store('ledgers', 'public');
        }

        Ledger::create([
            'name' => $request->name,
            'material' => $request->material,
            'price' => $request->price,
            'description' => $request->description,
            'picture' => $imagePath 
        ]);

        return redirect()->route('admin.ledgers.index')->with('success', 'New Ledger Product Added!');
    }

    // 4. SHOW EDIT FORM
    public function edit($id)
    {
        $ledger = Ledger::findOrFail($id);
        return view('admin.ledgers.edit', compact('ledger'));
    }

    // 5. UPDATE DATABASE
    public function update(Request $request, $id)
    {
        $ledger = Ledger::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'material' => 'required',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'material' => $request->material,
            'price' => $request->price,
            'description' => $request->description,
        ];

        // Handle Image Update
        if ($request->hasFile('picture')) {
            // Delete old image if it exists and isn't the placeholder
            if ($ledger->picture && $ledger->picture !== 'images/placeholder.jpg') {
                Storage::disk('public')->delete($ledger->picture);
            }

            // Store new image
            $data['picture'] = $request->file('picture')->store('ledgers', 'public');
        }

        $ledger->update($data);

        return redirect()->route('admin.ledgers.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $ledger = Ledger::findOrFail($id);
        
        if ($ledger->picture && $ledger->picture !== 'images/placeholder.jpg') {
            Storage::disk('public')->delete($ledger->picture);
        }

        $ledger->delete();
        return back()->with('success', 'Item deleted.');
    }
}