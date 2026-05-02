<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'User 1',
            'email' => 'user1@telsur.cl',
            'password' => Hash::make('password123'),
        ]);

        User::factory()->create([
            'name' => 'User 2',
            'email' => 'user2@telsur.cl',
            'password' => Hash::make('password123'),
        ]);
    }
}
