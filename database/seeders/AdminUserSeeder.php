<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@crow.lk',
            'password' => Hash::make('Apple@123'),
            'role' => 'admin', // Assuming 'admin' is the role for a super admin
        ]);

        // Assign the 'admin' role using Spatie's package
        $user->assignRole('admin');

        $this->command->info('Admin user created successfully.');
    }
}
