<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Order matters: ProductSeeder assigns categories, and OrderSeeder
        // needs both customers and products to already exist to attach to.
        $this->call([
            UserSeeder::class,
            EmployeeSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
