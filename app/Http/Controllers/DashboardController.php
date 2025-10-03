<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('is_active', true)->count();

        // Present = status Hadir/Checkout pada hari ini
        $todayPresent = Attendance::whereDate('date', $today)
            ->whereIn('status', ['Hadir', 'Checkout'])
            ->distinct('employee_id')
            ->count('employee_id');

        // Late = ada late_minutes > 0 pada hari ini
        $todayLate = Attendance::whereDate('date', $today)
            ->where('late_minutes', '>', 0)
            ->count();

        // Absent = status Tidak Hadir pada hari ini
        $todayAbsent = Attendance::whereDate('date', $today)
            ->where('status', 'Tidak Hadir')
            ->count();

        // Weekly stats for last 7 days (Mon..Sun style per now()->locale maybe not needed)
        $weeklyLabels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $startOfWeek = Carbon::now()->startOfWeek();
        $present = [];
        $late = [];
        $absent = [];
        for ($i = 0; $i < 7; $i++) {
            $day = (clone $startOfWeek)->addDays($i);
            $present[] = Attendance::whereDate('date', $day)
                ->whereIn('status', ['Hadir', 'Checkout'])
                ->distinct('employee_id')
                ->count('employee_id');
            $late[] = Attendance::whereDate('date', $day)
                ->where('late_minutes', '>', 0)
                ->count();
            $absent[] = Attendance::whereDate('date', $day)
                ->where('status', 'Tidak Hadir')
                ->count();
        }

        $weeklyAttendance = [
            'labels' => $weeklyLabels,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
        ];

        // Recent activities (latest 10)
        $recentActivities = Attendance::with('employee')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'todayPresent',
            'todayLate',
            'todayAbsent',
            'weeklyAttendance',
            'recentActivities'
        ));
    }
}
