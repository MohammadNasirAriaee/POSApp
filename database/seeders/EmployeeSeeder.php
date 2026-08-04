<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultEmployees = [
            [
                'first_name' => 'Alexander',
                'last_name' => 'Wright',
                'email' => 'alex.wright@posapp.com',
                'phone' => '+1 (555) 234-5678',
                'address' => '742 Evergreen Terrace, Suite 100',
                'position' => 'Store Manager',
                'salary' => 5800.00,
                'hire_date' => now()->subYears(3)->subMonths(2),
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'first_name' => 'Sophia',
                'last_name' => 'Chen',
                'email' => 'sophia.chen@posapp.com',
                'phone' => '+1 (555) 345-6789',
                'address' => '128 Innovation Way, Apt 4B',
                'position' => 'Assistant Manager',
                'salary' => 4500.00,
                'hire_date' => now()->subYears(2)->subMonths(5),
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'first_name' => 'Marcus',
                'last_name' => 'Johnson',
                'email' => 'marcus.j@posapp.com',
                'phone' => '+1 (555) 456-7890',
                'address' => '894 Market Street',
                'position' => 'Cashier',
                'salary' => 3200.00,
                'hire_date' => now()->subMonths(8),
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Rodriguez',
                'email' => 'emma.r@posapp.com',
                'phone' => '+1 (555) 567-8901',
                'address' => '312 Oak Boulevard',
                'position' => 'Inventory Specialist',
                'salary' => 3800.00,
                'hire_date' => now()->subYears(1)->subMonths(4),
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Miller',
                'email' => 'david.m@posapp.com',
                'phone' => '+1 (555) 678-9012',
                'address' => '505 Pine Avenue',
                'position' => 'Shift Supervisor',
                'salary' => 4100.00,
                'hire_date' => now()->subMonths(14),
                'status' => Employee::STATUS_ON_LEAVE,
            ],
            [
                'first_name' => 'Olivia',
                'last_name' => 'Taylor',
                'email' => 'olivia.t@posapp.com',
                'phone' => '+1 (555) 789-0123',
                'address' => '210 Commerce Lane',
                'position' => 'Sales Associate',
                'salary' => 3100.00,
                'hire_date' => now()->subMonths(4),
                'status' => Employee::STATUS_ACTIVE,
            ],
            [
                'first_name' => 'Lucas',
                'last_name' => 'Anderson',
                'email' => 'lucas.a@posapp.com',
                'phone' => '+1 (555) 890-1234',
                'address' => '678 Elm Drive',
                'position' => 'Cashier',
                'salary' => 2950.00,
                'hire_date' => now()->subMonths(2),
                'status' => Employee::STATUS_INACTIVE,
            ],
        ];

        foreach ($defaultEmployees as $data) {
            Employee::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        // Add 5 more random staff using factory if fewer than 10
        if (Employee::count() < 12) {
            Employee::factory(5)->create();
        }
    }
}
