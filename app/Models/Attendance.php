<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'late_minutes',
        'late_description',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper method untuk menghitung keterlambatan
    public function calculateLateness($checkInTime)
    {
        $workStartTime = Carbon::createFromTime(7, 0, 0); // 07:00
        $checkIn = Carbon::parse($checkInTime);
        
        if ($checkIn->gt($workStartTime)) {
            $diffInMinutes = $checkIn->diffInMinutes($workStartTime);
            
            if ($diffInMinutes >= 60) {
                $hours = floor($diffInMinutes / 60);
                $minutes = $diffInMinutes % 60;
                $description = "{$hours} jam";
                if ($minutes > 0) {
                    $description .= " {$minutes} menit";
                }
            } else {
                $description = "{$diffInMinutes} menit";
            }
            
            return [
                'minutes' => $diffInMinutes,
                'description' => "Terlambat {$description}"
            ];
        }
        
        return [
            'minutes' => 0,
            'description' => null
        ];
    }
}