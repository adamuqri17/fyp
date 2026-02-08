<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Grave;
use App\Models\Administrator;
use App\Models\Section;

class GraveRecoverySeeder extends Seeder
{
    public function run()
    {
        // 1. Prerequisites: We need at least 1 Admin and 1 Section.
        $admin = Administrator::first();
        if (!$admin) {
            $this->command->error('No Administrator found! Please create an admin first.');
            return;
        }

        $section = Section::first();
        if (!$section) {
            $section = Section::create(['section_name' => 'Section A']);
            $this->command->info('Created default Section A.');
        }

        // 2. Read the GeoJSON File
        $jsonPath = public_path('maps/map2.geojson');
        
        if (!File::exists($jsonPath)) {
            $this->command->error('File not found: ' . $jsonPath);
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (!isset($data['features'])) {
            $this->command->error('Invalid GeoJSON format.');
            return;
        }

        $count = 0;
        $this->command->info('Starting recovery...');

        // 3. Loop and Insert
        foreach ($data['features'] as $feature) {
            // CORRECTED: Use 'fid' as the identifier
            $geoId = $feature['properties']['fid'] ?? null;
            
            // Get Coordinates (GeoJSON is [Long, Lat])
            $lng = $feature['geometry']['coordinates'][0];
            $lat = $feature['geometry']['coordinates'][1];

            if ($geoId && $lat && $lng) {
                // Check if grave already exists to avoid duplicate errors
                $exists = Grave::where('grave_id', $geoId)->exists();

                if (!$exists) {
                    Grave::create([
                        'grave_id'   => $geoId, // Force the ID to match 'fid'
                        'admin_id'   => $admin->admin_id,
                        'section_id' => $section->section_id,
                        'latitude'   => $lat,
                        'longitude'  => $lng,
                        'status'     => 'available', // Default status
                    ]);
                    $count++;
                }
            }
        }

        $this->command->info("Successfully recovered {$count} graves from map2.geojson using 'fid'!");
    }
}