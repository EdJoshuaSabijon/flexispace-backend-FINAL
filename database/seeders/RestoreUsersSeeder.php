<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RestoreUsersSeeder extends Seeder
{
    /**
     * Restore admin and sample customer credentials.
     * Uses updateOrCreate so it is safe to run multiple times.
     */
    public function run(): void
    {
        // Restore admin account
        User::updateOrCreate(
            ['email' => 'admin@flexispace.com'],
            [
                'name'              => 'Admin FlexiSpace',
                'first_name'        => 'Admin',
                'last_name'         => 'FlexiSpace',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Restore sample customer account
        User::updateOrCreate(
            ['email' => 'customer@flexispace.com'],
            [
                'name'              => 'John Doe',
                'first_name'        => 'John',
                'last_name'         => 'Doe',
                'password'          => Hash::make('customer123'),
                'role'              => 'customer',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin and customer credentials restored.');
    }
}
