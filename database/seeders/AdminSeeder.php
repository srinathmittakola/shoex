<?php

namespace Database\Seeders;

use App\Models\Admin;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admins
        for ($i = 1; $i <= 5; $i++) {
            Admin::create([
                'name' => 'Admin ' . $i,
                'email' => 'admin' . $i . '@example.com',
                'password' => Hash::make('password'),
                'post' => 'admin',
            ]);
        }

        // One Manager post
        Admin::create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('managerpassword'),
            'post' => 'manager',
        ]);
    }
}
