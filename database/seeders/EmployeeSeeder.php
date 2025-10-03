<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'name' => 'Keysha',
            'employee_id' => 'EMP001',
            'rfid_uid' => 'CE5DC5',
            'department' => 'IT',
            'position' => 'Developer',
            'is_active' => true,
        ]);

        Employee::create([
            'name' => 'keysha cantik',
            'employee_id' => 'EMP002',
            'rfid_uid' => 'CFC4B2A6',
            'department' => 'HR',
            'position' => 'Manager',
            'is_active' => true,
        ]);

        Employee::create([
            'name' => 'keysha cantik sekali',
            'employee_id' => 'EMP003',
            'rfid_uid' => 'CB52AB4',
            'department' => 'Finance',
            'position' => 'Accountant',
            'is_active' => true,
        ]);
    }
}