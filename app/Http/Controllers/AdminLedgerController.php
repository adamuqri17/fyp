<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import Storage facade

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
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate Image
        ]);

        // Default placeholder if no image uploaded
        $imagePath = 'images/placeholder.jpg'; 

        // Handle File Upload
        if ($request->hasFile('picture')) {
            // Stores in 'storage/app/public/ledgers' and returns the path
            $imagePath = $request->file('picture')->store('ledgers', 'public');
        }

        Ledger::create([
            'name' => $request->name,
            'material' => $request->material,
            'price' => $request->price,
            'description' => $request->description,
            'picture' => $imagePath // Save the path, not the file itself
        ]);

        return redirect()->route('admin.ledgers.index')->with('success', 'New Ledger Product Added!');
    }

    public function destroy($id)
    {
        $ledger = Ledger::findOrFail($id);
        
        // Optional: Delete the image file when deleting the record
        if ($ledger->picture && $ledger->picture !== 'images/placeholder.jpg') {
            Storage::disk('public')->delete($ledger->picture);
        }

        $ledger->delete();
        return back()->with('success', 'Item deleted.');
    }
}