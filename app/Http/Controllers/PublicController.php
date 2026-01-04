<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deceased;

class PublicController extends Controller
{
    // Existing Search Function
    public function search(Request $request)
    {
        $keyword = $request->input('keyword'); 
        $results = collect();

        if ($keyword) {
            $results = Deceased::with(['grave.section'])
                ->where('full_name', 'LIKE', "%{$keyword}%")
                ->orWhere('ic_number', 'LIKE', "%{$keyword}%")
                ->get();
        }

        return view('public.results', compact('results', 'keyword'));
    }

    // === NEW FUNCTION: DIRECTORY ===
    public function directory()
    {
        // Get all records, sorted alphabetically, 20 per page
        $deceaseds = Deceased::with(['grave.section'])
                             ->orderBy('full_name', 'asc')
                             ->paginate(20);

        return view('public.directory', compact('deceaseds'));
    }

    // ... inside PublicController class ...

    public function home()
    {
        // 1. Count Real Data
        $totalDeceased = Deceased::count();
        $totalAvailable = \App\Models\Grave::where('status', 'available')->count();
        
        // 2. Count Distinct Sections (Zones)
        $totalZones = \App\Models\Section::count();

        return view('homepage', compact('totalDeceased', 'totalAvailable', 'totalZones'));
    }
}