<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Check if superadmin already exists
        if (!User::where('username', 'superadmin')->exists()) {

            User::create([
                'first_name' => 'Solo',
                'middle_name' => 'Care',
                'last_name' => 'SuperAdmin',
                'username' => 'superadmin',
                'email' => 'superadmin@solocare.com',
                'contact' => '09123456789',
                'street' => 'Super Admin Street',
                'barangay' => 'Super Admin Barangay',
                'municipality_city' => 'Super Admin City',
                'province' => 'Super Admin Province',
                'password' => 'SuperAdmin123!', // will be hashed automatically by the model
                'role' => User::ROLE_SUPER_ADMIN,
                'status' => 'Active',
            ]);
        }
    }
}
