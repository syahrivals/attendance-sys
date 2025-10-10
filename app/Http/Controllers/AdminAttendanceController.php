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

    protected function parseIds($ids): array
    {
        if (is_string($ids)) {
            $ids = preg_split('/[,\\s]+/', $ids, -1, PREG_SPLIT_NO_EMPTY);
        }

        if ($ids instanceof \Illuminate\Support\Collection) {
            $ids = $ids->all();
        }

        if (! is_array($ids)) {
            $ids = [$ids];
        }

        return collect($ids)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function exportPreview(Request $request)
    {
        $ids = $this->parseIds($request->input('ids', []));

        if (empty($ids)) {
            return response()->json([
                'message' => 'Tidak ada data absensi yang dipilih.',
            ], 422);
        }

        $attendances = Attendance::with('employee')
            ->whereIn('id', $ids)
            ->orderByDesc('date')
            ->orderBy('employee_id')
            ->get();

        if ($attendances->isEmpty()) {
            return response()->json([
                'message' => 'Data absensi tidak ditemukan.',
            ], 404);
        }

        $data = $attendances->map(function (Attendance $attendance) {
            $employee = $attendance->employee;

            return [
                'id' => $attendance->id,
                'employee' => [
                    'id' => $employee?->id,
                    'name' => $employee?->name,
                    'employee_id' => $employee?->employee_id,
                    'department' => $employee?->department,
                    'position' => $employee?->position,
                ],
                'date' => optional($attendance->date)->format('Y-m-d'),
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'status' => $attendance->status,
                'late_minutes' => $attendance->late_minutes,
                'notes' => $attendance->notes,
            ];
        });

        return response()->json([
            'count' => $data->count(),
            'attendances' => $data,
        ]);
    }

    public function exportDownload(Request $request)
    {
        $ids = $this->parseIds($request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu data absensi untuk diekspor.');
        }

        $attendances = Attendance::with('employee')
            ->whereIn('id', $ids)
            ->orderByDesc('date')
            ->orderBy('employee_id')
            ->get();

        if ($attendances->isEmpty()) {
            return back()->with('error', 'Data absensi tidak ditemukan.');
        }

        $filename = 'attendance-export-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = ['ID', 'Tanggal', 'NIP', 'Nama', 'Departemen', 'Posisi', 'Check In', 'Check Out', 'Status', 'Terlambat (menit)', 'Catatan'];

        $callback = function () use ($attendances, $columns) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $columns);

            foreach ($attendances as $attendance) {
                $employee = $attendance->employee;

                fputcsv($handle, [
                    $attendance->id,
                    optional($attendance->date)->format('Y-m-d'),
                    $employee?->employee_id,
                    $employee?->name,
                    $employee?->department,
                    $employee?->position,
                    $attendance->check_in_time,
                    $attendance->check_out_time,
                    $attendance->status,
                    $attendance->late_minutes,
                    $attendance->notes,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $this->parseIds($request->input('ids', []));

        if (empty($ids)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Pilih minimal satu data absensi.',
                ], 422);
            }

            return back()->with('error', 'Pilih minimal satu data absensi.');
        }

        $deleted = Attendance::whereIn('id', $ids)->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'deleted' => $deleted,
            ]);
        }

        return back()->with('success', "Berhasil menghapus {$deleted} data absensi.");
    }
}

