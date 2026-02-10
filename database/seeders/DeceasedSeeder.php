<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grave;
use App\Models\Deceased;
use App\Models\Administrator;
use Faker\Factory as Faker;

class DeceasedSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ms_MY'); // Use Malaysian locale for realistic names/IC
        
        // 1. Get a default Admin ID for the 'admin_id' field
        $admin = Administrator::first();
        if (!$admin) {
            $this->command->error('No Admin found. Please seed administrators first.');
            return;
        }

        $count = 0;
        $this->command->info('Seeding Deceased records for graves 1-800...');

        // 2. Loop through Grave IDs 1 to 800
        for ($i = 1; $i <= 800; $i++) {
            
            $grave = Grave::find($i);

            // Only proceed if grave exists and is NOT already occupied
            if ($grave && $grave->status !== 'occupied') {
                
                // 3. Define Logic: 1-500 Male, 501-800 Female
                $gender = ($i <= 500) ? 'Male' : 'Female';
                $name = ($gender === 'Male') ? $faker->name('male') : $faker->name('female');
                
                // Generate logic dates
                $dob = $faker->dateTimeBetween('-80 years', '-20 years');
                $dod = $faker->dateTimeBetween('-5 years', 'now');
                $burialDate = (clone $dod)->modify('+1 day'); // Buried 1 day after death

                // 4. Create Deceased Record
                Deceased::create([
                    'grave_id'      => $grave->grave_id,
                    'admin_id'      => $admin->admin_id,
                    'full_name'     => $name,
                    'ic_number'     => $faker->unique()->numerify('######-##-####'),
                    'gender'        => $gender,
                    'date_of_birth' => $dob->format('Y-m-d'),
                    'date_of_death' => $dod->format('Y-m-d'),
                    'burial_date'   => $burialDate->format('Y-m-d'),
                    'notes'         => 'Seeded data',
                ]);

                // 5. Update Grave Status
                $grave->update(['status' => 'occupied']);
                
                $count++;
            }
        }

        $this->command->info("Successfully seeded {$count} deceased records!");
    }
}