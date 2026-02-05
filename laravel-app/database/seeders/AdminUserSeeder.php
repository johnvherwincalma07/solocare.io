<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
           ['username' => 'admin'], // unique field to avoid duplicates
            [
                'fullname' => 'Solo Care',
                'email' => 'solocare.service@gmail.com',
                'contact' => '09123456789',
                'address' => 'Admin Address',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'status' => 'Active',
            ]
        );
    }
}
