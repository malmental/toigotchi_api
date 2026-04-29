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
            'name' => 'Salem',
            'email' => 'salem@telsur.cl',
            'password' => Hash::make('password123'),
        ]);

        User::factory()->create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
