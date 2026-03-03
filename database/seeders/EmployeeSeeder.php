<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'employee_code' => 1,
            'name' => 'Ahmed Al-Zahrani',
            'phone' => '0501234567',
            'daily_salary' => 150.00,
            'current_balance' => 0,
            'status' => 'active',
        ]);

        Employee::create([
            'employee_code' => 2,
            'name' => 'Fatima Al-Otaibi',
            'phone' => '0509876543',
            'daily_salary' => 120.00,
            'current_balance' => 0,
            'status' => 'active',
        ]);

        Employee::create([
            'employee_code' => 3,
            'name' => 'Mohammed Al-Dossary',
            'phone' => '0551111111',
            'daily_salary' => 180.00,
            'current_balance' => 0,
            'status' => 'active',
        ]);

        Employee::create([
            'employee_code' => 4,
            'name' => 'Sarah Al-Shammari',
            'phone' => '0552222222',
            'daily_salary' => 100.00,
            'current_balance' => 0,
            'status' => 'active',
        ]);

        Employee::create([
            'employee_code' => 5,
            'name' => 'Ali Al-Muraikhi',
            'phone' => '0553333333',
            'daily_salary' => 160.00,
            'current_balance' => 0,
            'status' => 'active',
        ]);

        Employee::create([
            'employee_code' => 6,
            'name' => 'Noor Al-Enezi',
            'phone' => null,
            'daily_salary' => 130.00,
            'current_balance' => 0,
            'status' => 'active',
        ]);
    }
}
