<?php

namespace App\Http\Controllers;

use App\Models\Grave;
use App\Models\Section;
use App\Models\Deceased;
use App\Models\LedgerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminGraveController extends Controller
{
    /**
     * 0. ADMIN DASHBOARD
     */
    public function dashboard()
    {
        $totalGraves = Grave::count();
        $available   = Grave::where('status', 'available')->count();
        $occupied    = Grave::where('status', 'occupied')->count();
        $ledgerOrders = LedgerOrder::count();

        $recentDeceased = Deceased::with('grave.section')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact('totalGraves', 'available', 'occupied', 'ledgerOrders', 'recentDeceased'));
    }
    
    /**
     * 1. LIST ALL GRAVES
     */
    public function index()
    {
        $graves = Grave::with('section')->latest()->paginate(10);
        return view('admin.graves.index', compact('graves'));
    }

    /**
     * 2. SHOW CREATE FORM
     */
    public function create()
    {
        $sections = Section::all();
        
        // Fetch existing graves to show as obstacles on the creation map
        $existingGraves = Grave::select('grave_id', 'latitude', 'longitude', 'status')->get();

        return view('admin.graves.create', compact('sections', 'existingGraves'));
    }

    /**
     * 3. STORE NEW GRAVE
     */
    public function store(Request $request)
    {
        // A. Validation
        $rules = [
            'section_id' => 'required|exists:sections,section_id',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'status'     => 'required|in:available,occupied,reserved',
        ];

        // Conditional Rules for Deceased
        if ($request->status === 'occupied') {
            $rules['full_name']     = 'required|string|max:255';
            $rules['ic_number']     = 'required|string|unique:deceased,ic_number';
            $rules['gender']        = 'required|in:Male,Female';
            $rules['date_of_death'] = 'required|date';
            $rules['burial_date']   = 'nullable|date';
        }

        $validated = $request->validate($rules);

        // B. Database Transaction
        DB::transaction(function () use ($validated, $request) {
            
            // 1. Create Grave (grave_id is auto-increment, no manual grave_number)
            $grave = Grave::create([
                'section_id'   => $validated['section_id'],
                'admin_id'     => Auth::guard('admin')->id(),
                'latitude'     => $validated['latitude'],
                'longitude'    => $validated['longitude'],
                'status'       => $validated['status'],
            ]);

            // 2. Create Deceased Record (Only if Occupied)
            if ($validated['status'] === 'occupied') {
                Deceased::create([
                    'grave_id'      => $grave->grave_id,
                    'admin_id'      => Auth::guard('admin')->id(),
                    'full_name'     => $validated['full_name'],
                    'ic_number'     => $validated['ic_number'],
                    'gender'        => $validated['gender'],
                    'date_of_birth' => $request->date_of_birth, // Optional
                    'date_of_death' => $validated['date_of_death'],
                    'burial_date'   => $validated['burial_date'],
                ]);
            }
        });

        return redirect()->route('admin.map.manager')
            ->with('success', 'Grave Plot created successfully!');
    }

    /**
     * 4. SHOW EDIT FORM
     */
    public function edit($id)
    {
        // Fetch the specific grave with deceased info
        $grave = Grave::with('deceased')->findOrFail($id);
        $sections = Section::all();
        
        // CRITICAL: Fetch ALL other graves to show as obstacles
        // We exclude the current ID so the user doesn't collide with the grave they are moving
        $otherGraves = Grave::where('grave_id', '!=', $id)
                            ->select('grave_id', 'latitude', 'longitude', 'status')
                            ->get();

        return view('admin.graves.edit', compact('grave', 'sections', 'otherGraves'));
    }

    /**
     * 5. UPDATE EXISTING GRAVE (Modified for Deletion Logic)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'section_id' => 'required',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'status'     => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {
            $grave = Grave::findOrFail($id);
            
            // 1. Update Grave Location & Status
            $grave->update([
                'section_id' => $request->section_id,
                'latitude'   => $request->latitude,
                'longitude'  => $request->longitude,
                'status'     => $request->status,
            ]);

            // 2. Update/Create OR Delete Deceased Record
            if ($request->status === 'occupied') {
                // If status is Occupied, update or create the info
                Deceased::updateOrCreate(
                    ['grave_id' => $grave->grave_id],
                    [
                        'admin_id'      => Auth::guard('admin')->id(),
                        'full_name'     => $request->full_name,
                        'ic_number'     => $request->ic_number,
                        'gender'        => $request->gender,
                        'date_of_birth' => $request->date_of_birth,
                        'date_of_death' => $request->date_of_death,
                        'burial_date'   => $request->burial_date,
                    ]
                );
            } else {
                // If status is changed to Available or Reserved, DELETE the deceased record
                $grave->deceased()->delete();
            }
        });

        // Redirect back to Map Manager to verify new location
        return redirect()->route('admin.map.manager')->with('success', 'Grave updated successfully');
    }

    /**
     * 6. DELETE GRAVE
     */
    public function destroy($id)
    {
        $grave = Grave::findOrFail($id);
        $grave->delete();

        return redirect()->route('admin.graves.index')
            ->with('success', 'Grave plot deleted.');
    }

    /**
     * 7. VISUAL MAP MANAGER
     */
    public function mapManager()
    {
        $graves = Grave::with(['deceased', 'section'])->get();
        return view('map', compact('graves')); 
    }
}