<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::paginate(15);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'employee_id' => 'required|unique:employees',
            'rfid_uid' => 'required|unique:employees',
        ]);

        Employee::create($request->all());

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.form', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required',
            'employee_id' => 'required|unique:employees,employee_id,' . $employee->id,
            'rfid_uid' => 'required|unique:employees,rfid_uid,' . $employee->id,
        ]);

        $employee->update($request->all());

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
