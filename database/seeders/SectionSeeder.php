<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        Section::create(['section_name' => 'Section A', 'description' => 'Male Plots']);
        Section::create(['section_name' => 'Section B', 'description' => 'Female Plots']);
        Section::create(['section_name' => 'Section C', 'description' => 'Children Plots']);
    }
}