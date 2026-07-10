<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@highfive.id'],
            [
                'name' => 'Admin HIGH FIVE',
                'email' => 'admin@highfive.id',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@highfive.id'],
            [
                'name' => 'John Doe',
                'email' => 'user@highfive.id',
                'password' => Hash::make('password'),
                'phone' => '089876543210',
                'role' => 'pengunjung',
                'email_verified_at' => now(),
            ]
        );
    }
}
