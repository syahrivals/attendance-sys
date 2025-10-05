<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        // Filters
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->input('end_date'));
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('department')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department', $request->input('department'));
            });
        }

        $attendances = $query->orderByDesc('date')->orderBy('check_in_time')->paginate(15);

        // Summary numbers
        $today = Carbon::today();
        $todayTotal = Attendance::whereDate('date', $today)->count();
        $todayPresent = Attendance::whereDate('date', $today)->whereIn('status', ['Hadir', 'Checkout'])->distinct('employee_id')->count('employee_id');
        $todayLate = Attendance::whereDate('date', $today)->where('late_minutes', '>', 0)->count();
        $todayAbsent = Attendance::whereDate('date', $today)->where('status', 'Tidak Hadir')->count();
        $attendanceRate = $todayTotal > 0 ? round(($todayPresent / max($todayTotal, 1)) * 100) : 0;

        $employees = Employee::orderBy('name')->get();
        $departments = Employee::query()->whereNotNull('department')->distinct()->pluck('department')->filter()->values()->all();

        return view('admin.attendance.index', compact(
            'attendances',
            'employees',
            'departments',
            'todayTotal',
            'todayPresent',
            'todayLate',
            'todayAbsent',
            'attendanceRate'
        ));
    }
}


