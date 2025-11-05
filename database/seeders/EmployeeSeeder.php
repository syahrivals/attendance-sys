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
            'face_uid' => 'syahrival',
            'department' => 'IT',
            'position' => 'Developer',
            'is_active' => true,
        ]);

        Employee::create([
            'name' => 'keysha cantik',
            'employee_id' => 'EMP002',
            'rfid_uid' => '4444F2F46E80',
            'face_uid' => 'gilank',
            'department' => 'HR',
            'position' => 'Manager',
            'is_active' => true,
        ]);

        Employee::create([
            'name' => 'keysha cantik sekali',
            'employee_id' => 'EMP003',
            'rfid_uid' => 'CB52AB4',
            'face_uid' => 'andika',
            'department' => 'Finance',
            'position' => 'Accountant',
            'is_active' => true,
        ]);
    }
}