<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



class AttendanceController extends Controller
{
    /**
     * Endpoint untuk menerima data dari ESP32
     * POST /api/attendance/tap
     */
    public function tap(Request $request)
    {
        try {
            // Validasi input fleksibel: terima rfid_uid ATAU face_uid
            $request->validate([
                'device_id' => 'required|string',
                'rfid_uid' => 'nullable|string',
                'face_uid' => 'nullable|string',
            ]);

            if (!$request->filled('rfid_uid') && !$request->filled('face_uid')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Input tidak valid: butuh salah satu dari rfid_uid atau face_uid',
                    'buzzer' => 'error'
                ], 422);
            }

            $rfidUid = $request->input('rfid_uid');
            $faceUid = $request->input('face_uid');
            $deviceId = $request->device_id;
            $currentTime = Carbon::now('Asia/Jakarta');
            $today = today();

            Log::info("Attendance tap received", [
                'rfid_uid' => $rfidUid,
                'face_uid' => $faceUid,
                'device_id' => $deviceId,
                'time' => $currentTime
            ]);

            // Resolve karyawan berdasarkan prioritas: rfid_uid -> face_uid
            $employeeQuery = Employee::query()->where('is_active', true);
            if ($rfidUid) {
                $employeeQuery->where('rfid_uid', $rfidUid);
            } elseif ($faceUid) {
                $employeeQuery->where('face_uid', $faceUid);
            }
            $employee = $employeeQuery->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan / tidak aktif',
                    'buzzer' => 'error' // Signal untuk buzzer error
                ], 404);
            }

            // Cek apakah sudah ada record kehadiran hari ini
            $attendance = Attendance::where('employee_id', $employee->id)
                                   ->where('date', $today)
                                   ->first();

            if (!$attendance) {
                // First tap today - CHECK IN
                $attendance = new Attendance();
                $attendance->employee_id = $employee->id;
                $attendance->date = $today;
                $attendance->check_in_time = $currentTime->format('H:i:s');
                $attendance->status = 'Hadir';

                // Hitung keterlambatan
                $latenessData = $attendance->calculateLateness($currentTime);
                $attendance->late_minutes = $latenessData['minutes'];
                $attendance->late_description = $latenessData['description'];

                $attendance->save();

                $message = "Check-in berhasil: {$employee->name}";
                if ($latenessData['minutes'] > 0) {
                    $message .= " ({$latenessData['description']})";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'action' => 'check_in',
                    'employee' => $employee->name,
                    'time' => $currentTime->format('H:i:s'),
                    'late_minutes' => $latenessData['minutes'],
                    'buzzer' => 'success'
                ]);

            } else {
                // Second tap today - CHECK OUT
                if ($attendance->status === 'Hadir') {
                    $attendance->check_out_time = $currentTime->format('H:i:s');
                    $attendance->status = 'Checkout';
                    $attendance->save();

                    return response()->json([
                        'success' => true,
                        'message' => "Check-out berhasil: {$employee->name}",
                        'action' => 'check_out',
                        'employee' => $employee->name,
                        'check_in_time' => $attendance->check_in_time,
                        'check_out_time' => $currentTime->format('H:i:s'),
                        'buzzer' => 'success'
                    ]);
                } else {
                    // Sudah checkout, tidak bisa tap lagi
                    return response()->json([
                        'success' => false,
                        'message' => "Sudah checkout hari ini: {$employee->name}",
                        'buzzer' => 'warning'
                    ], 400);
                }
            }

        } catch (\Exception $e) {
            Log::error("Attendance tap error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'buzzer' => 'error'
            ], 500);
        }
    }

    /**
     * Endpoint untuk mendapatkan status kehadiran hari ini
     * GET /api/attendance/today
     */
    public function todayAttendances()
    {
        try {
            $attendances = Attendance::with('employee')
                                   ->where('date', today())
                                   ->orderBy('check_in_time')
                                   ->get();

            return response()->json([
                'success' => true,
                'data' => $attendances->map(function ($attendance) {
                    return [
                        'employee_name' => $attendance->employee->name,
                        'employee_id' => $attendance->employee->employee_id,
                        'check_in_time' => $attendance->check_in_time,
                        'check_out_time' => $attendance->check_out_time,
                        'status' => $attendance->status,
                        'late_minutes' => $attendance->late_minutes,
                        'late_description' => $attendance->late_description
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kehadiran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint untuk reset kehadiran harian (bisa dipanggil cron job)
     * POST /api/attendance/daily-reset
     */
    public function dailyReset()
    {
        try {
            // Set semua karyawan yang belum hadir hari ini sebagai "Tidak Hadir"
            $employees = Employee::where('is_active', true)->get();
            $today = today();

            foreach ($employees as $employee) {
                $hasAttendance = Attendance::where('employee_id', $employee->id)
                                         ->where('date', $today)
                                         ->exists();

                if (!$hasAttendance) {
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $today,
                        'status' => 'Tidak Hadir'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Daily reset completed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Daily reset failed: ' . $e->getMessage()
            ], 500);
        }
    }
}