<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\Grave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDeceasedController extends Controller
{
    // 1. LIST ALL DECEASED (New)
    public function index(Request $request)
    {
        $query = Deceased::with('grave.section');

        // Simple Search Filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('ic_number', 'LIKE', "%{$search}%");
        }

        $deceaseds = $query->latest()->paginate(10);

        return view('admin.deceased.index', compact('deceaseds'));
    }

    // 2. SHOW CREATE FORM (Existing)
    public function create()
    {
        $availableGraves = Grave::with('section')
                                ->where('status', 'available')
                                ->get();
        return view('admin.deceased.create', compact('availableGraves'));
    }

    // 3. STORE NEW RECORD (Existing - Minor update to ensure redirects to index)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'   => 'required|string|max:255',
            'ic_number'   => 'required|string|max:20|unique:deceased,ic_number',
            'gender'      => 'required|in:Male,Female',
            'dob'         => 'required|date',
            'dod'         => 'required|date|after_or_equal:dob',
            'grave_id'    => 'required|exists:graves,grave_id',
            'notes'       => 'nullable|string',
        ]);

        Deceased::create([
            'grave_id'      => $validated['grave_id'],
            'admin_id'      => Auth::guard('admin')->id(),
            'full_name'     => $validated['full_name'],
            'ic_number'     => $validated['ic_number'],
            'gender'        => $validated['gender'],
            'date_of_birth' => $validated['dob'],
            'date_of_death' => $validated['dod'],
            'burial_date'   => now(),
            'notes'         => $validated['notes'],
        ]);

        // Update Grave Status
        $grave = Grave::find($validated['grave_id']);
        $grave->status = 'occupied';
        $grave->save();

        // Redirect to the LIST instead of the map
        return redirect()->route('admin.deceased.index')
            ->with('success', 'Deceased record registered successfully.');
    }

    // 4. SHOW EDIT FORM (New)
    public function edit($id)
    {
        $deceased = Deceased::findOrFail($id);
        return view('admin.deceased.edit', compact('deceased'));
    }

    // 5. UPDATE RECORD (New)
    public function update(Request $request, $id)
    {
        $deceased = Deceased::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'ic_number' => 'required|string|max:20|unique:deceased,ic_number,' . $id . ',deceased_id', // Ignore self
            'gender'    => 'required|in:Male,Female',
            'dob'       => 'required|date',
            'dod'       => 'required|date|after_or_equal:dob',
            'notes'     => 'nullable|string',
        ]);

        $deceased->update([
            'full_name'     => $validated['full_name'],
            'ic_number'     => $validated['ic_number'],
            'gender'        => $validated['gender'],
            'date_of_birth' => $validated['dob'],
            'date_of_death' => $validated['dod'],
            'notes'         => $validated['notes'],
        ]);

        return redirect()->route('admin.deceased.index')
            ->with('success', 'Deceased record updated.');
    }

    // 6. DELETE RECORD (New - Important Logic Here!)
    public function destroy($id)
    {
        $deceased = Deceased::findOrFail($id);
        $graveId = $deceased->grave_id;

        // A. Delete the person
        $deceased->delete();

        // B. Free up the grave (Make it Green again)
        $grave = Grave::find($graveId);
        if ($grave) {
            $grave->status = 'available';
            $grave->save();
        }

        return redirect()->route('admin.deceased.index')
            ->with('success', 'Record deleted and grave plot is now Available.');
    }
}