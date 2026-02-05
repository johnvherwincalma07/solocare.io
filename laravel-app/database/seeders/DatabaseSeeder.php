<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Remove any existing admin
        User::whereIn('username', ['admin', 'superadmin'])->delete();

        // Reset auto-increment to 1 (optional)
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1;');

        // Create the admin account
        User::create([
            'fullname' => 'Solo Care Admin',
            'username' => 'admin',
            'email' => 'solocare.service@gmail.com',
            'contact' => '09123456789',
            'address' => 'Admin Address',
            'password' => 'Admin123!', // will be hashed automatically
            'confirm_password' => 'Admin123!', // will be hashed automatically
            'role' => 'admin',
            'status' => 'Active',
        ]);

        User::create([
            'fullname' => 'Solo Care Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@solocare.com',
            'contact' => '09987654321',
            'address' => 'Super Admin Address',
            'password' => 'SuperAdmin123!',
            'role' => 'super_admin',
            'status' => 'Active',
        ]);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
