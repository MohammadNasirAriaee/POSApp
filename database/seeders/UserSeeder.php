<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@posapp.test',
            'password' => Hash::make('password'),
        ]);

        // Cashier User
        User::create([
            'name' => 'John Cashier',
            'email' => 'john@posapp.test',
            'password' => Hash::make('password'),
        ]);

        // Manager User
        User::create([
            'name' => 'Jane Manager',
            'email' => 'jane@posapp.test',
            'password' => Hash::make('password'),
        ]);
    }
}
