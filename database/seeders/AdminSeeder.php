<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Administrator::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'name' => 'System Admin',
            'phone' => '0123456789',
        ]);

        Administrator::create([
            'username' => 'adam',  // <--- Your preferred username
            'password' => Hash::make('adam123'), // <--- Your preferred password
            'name' => 'Adam Muqri',
            'phone' => '011-11111111',
        ]);
    }
}