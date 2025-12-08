<?php

namespace App\Http\Controllers;

use App\Models\Grave;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminGraveController extends Controller
{
    // 1. LIST ALL GRAVES
    public function index()
    {
        // Fetch graves with their section info, paginated (10 per page)
        $graves = Grave::with('section')->latest()->paginate(10);
        return view('admin.graves.index', compact('graves'));
    }

    // 2. SHOW CREATE FORM
    public function create()
    {
        $sections = Section::all();
        $existingGraves = Grave::select('grave_id', 'latitude', 'longitude', 'status')->get();

        return view('admin.graves.create', compact('sections', 'existingGraves'));
    }

    // 3. STORE NEW GRAVE (The actual saving part)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'status'     => 'required|in:available,occupied,reserved',
        ]);

        // Create the grave
        Grave::create([
            'section_id' => $validated['section_id'],
            'admin_id'   => Auth::guard('admin')->id(), // Link to current admin
            'latitude'   => $validated['latitude'],
            'longitude'  => $validated['longitude'],
            'status'     => $validated['status'],
        ]);

        return redirect()->route('admin.graves.index')
            ->with('success', 'Grave plot created successfully.');
    }

    // 4. SHOW EDIT FORM
    public function edit($id)
    {
        $grave = Grave::findOrFail($id);
        $sections = Section::all();
        return view('admin.graves.edit', compact('grave', 'sections'));
    }

    // 5. UPDATE EXISTING GRAVE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,section_id',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'status'     => 'required|in:available,occupied,reserved',
        ]);

        $grave = Grave::findOrFail($id);
        $grave->update($validated);

        return redirect()->route('admin.graves.index')
            ->with('success', 'Grave plot updated successfully.');
    }

    // 6. DELETE GRAVE
    public function destroy($id)
    {
        $grave = Grave::findOrFail($id);
        $grave->delete();

        return redirect()->route('admin.graves.index')
            ->with('success', 'Grave plot deleted.');
    }

    // 7. VISUAL MAP MANAGER
    public function mapManager()
    {
        // Fetch all graves with deceased info
        $graves = Grave::with('deceased')->get();
        return view('admin.graves.map-manager', compact('graves'));
    }
}