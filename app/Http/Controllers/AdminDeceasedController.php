<?php

namespace App\Http\Controllers;

use App\Models\Deceased;
use App\Models\Grave;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDeceasedController extends Controller
{
    // ... index method ...
    public function index(Request $request)
    {
        $query = Deceased::with('grave.section');
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('full_name', 'LIKE', "%{$search}%")->orWhere('ic_number', 'LIKE', "%{$search}%");
        }
        $deceaseds = $query->latest()->paginate(10);
        return view('admin.deceased.index', compact('deceaseds'));
    }

    // 1. SHOW CREATE FORM (Handles both Manual & Map flows)
    public function create(Request $request)
    {
        $sections = Section::all();
        
        // Handle "Register" click from Map
        $preselectedGraveId = $request->query('grave_id');
        $preselectedSectionId = null;
        $preselectedPlots = collect(); 

        if ($preselectedGraveId) {
            $grave = Grave::find($preselectedGraveId);
            if ($grave) {
                $preselectedSectionId = $grave->section_id;
                
                // Pre-fetch plots so the map flow works instantly without waiting for AJAX
                $preselectedPlots = Grave::where('section_id', $preselectedSectionId)
                    ->where(function($q) use ($preselectedGraveId) {
                        $q->where('status', 'available')
                          ->orWhere('grave_id', $preselectedGraveId);
                    })
                    ->orderBy('grave_id', 'asc')
                    ->get();
            }
        }

        return view('admin.deceased.create', compact('sections', 'preselectedGraveId', 'preselectedSectionId', 'preselectedPlots'));
    }

    // 2. AJAX: GET AVAILABLE PLOTS BY SECTION (Crucial for Manual Selection)
    public function getPlots(Request $request)
    {
        $sectionId = $request->section_id;
        
        if(!$sectionId) return response()->json([]);

        $plots = Grave::where('section_id', $sectionId)
                      ->where('status', 'available')
                      ->select('grave_id')
                      ->orderBy('grave_id', 'asc')
                      ->get();

        return response()->json($plots);
    }

    // ... store, edit, update, destroy methods ...
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'   => 'required|string|max:255',
            'ic_number'   => 'required|string|max:20|unique:deceased,ic_number',
            'gender'      => 'required|in:Male,Female',
            'dob'         => 'required|date',
            'dod'         => 'required|date|after_or_equal:dob',
            'burial_date' => 'required|date|after_or_equal:dod',
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
            'burial_date'   => $validated['burial_date'], 
            'notes'         => $validated['notes'],
        ]);

        $grave = Grave::find($validated['grave_id']);
        $grave->status = 'occupied';
        $grave->save();

        return redirect()->route('admin.deceased.index')->with('success', 'Deceased record registered successfully.');
    }
    
    public function edit($id) { $deceased = Deceased::findOrFail($id); return view('admin.deceased.edit', compact('deceased')); }
    public function update(Request $request, $id) { 
        $deceased = Deceased::findOrFail($id);
        $deceased->update($request->all());
        return redirect()->route('admin.deceased.index')->with('success', 'Updated.'); 
    }
    public function destroy($id) { 
        $dec = Deceased::findOrFail($id); 
        Grave::where('grave_id', $dec->grave_id)->update(['status'=>'available']); 
        $dec->delete(); 
        return redirect()->route('admin.deceased.index'); 
    }
}