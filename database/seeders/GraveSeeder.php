<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grave;
use App\Models\Section;

class GraveSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get Admin
        $admin = \App\Models\Administrator::first();
        $adminId = $admin ? $admin->admin_id : 1;

        // 2. Create Sections based on PDF
        $sections = [
            'A' => Section::firstOrCreate(['section_name' => 'Lot A', 'description' => 'Kubur Lelaki (Male)']),
            'B' => Section::firstOrCreate(['section_name' => 'Lot B', 'description' => 'Kubur Perempuan (Female)']),
            'D' => Section::firstOrCreate(['section_name' => 'Lot D', 'description' => 'Kubur Lelaki (Male)']),
        ];

        // 3. Global Spacing Configuration
        $latGap = 0.000060; // Vertical spacing between rows
        $lngGap = 0.000045; // Horizontal spacing between columns

        // --- GENERATE LOT A (Top Left) ---
        // 4 Rows x 6 Columns
        $this->createBlock($sections['A']->section_id, $adminId, 2.97400, 101.48820, 4, 6, $latGap, $lngGap);

        // --- GENERATE LOT B (Bottom Left - Below A) ---
        // Start Latitude is lower to leave a walking path gap
        // 4 Rows x 6 Columns
        $this->createBlock($sections['B']->section_id, $adminId, 2.97370, 101.48820, 4, 6, $latGap, $lngGap);

        // --- GENERATE LOT D (Right Side - Across Center Path) ---
        // Start Longitude is higher to leave space for "Simpanan Jalan"
        // 5 Rows x 5 Columns
        $this->createBlock($sections['D']->section_id, $adminId, 2.97390, 101.48865, 5, 5, $latGap, $lngGap);
    }

    // Helper function to generate a grid block
    private function createBlock($sectionId, $adminId, $startLat, $startLng, $rows, $cols, $latGap, $lngGap) 
    {
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                
                // Calculate position
                $lat = $startLat - ($r * $latGap);
                $long = $startLng + ($c * $lngGap);

                Grave::create([
                    'section_id' => $sectionId,
                    'admin_id'   => $adminId,
                    'latitude'   => $lat,
                    'longitude'  => $long,
                    'status'     => 'available',
                ]);
            }
        }
    }
}