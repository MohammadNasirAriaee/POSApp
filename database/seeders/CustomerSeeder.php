<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@example.com', 'phone' => '555-0101'],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane.smith@example.com', 'phone' => '555-0102'],
            ['first_name' => 'Michael', 'last_name' => 'Johnson', 'email' => 'michael.j@example.com', 'phone' => '555-0103'],
            ['first_name' => 'Emily', 'last_name' => 'Davis', 'email' => 'emily.davis@example.com', 'phone' => '555-0104'],
            ['first_name' => 'David', 'last_name' => 'Wilson', 'email' => 'david.wilson@example.com', 'phone' => '555-0105'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
