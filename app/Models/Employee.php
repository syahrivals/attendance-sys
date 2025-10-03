<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employee_id',
        'rfid_uid',
        'department',
        'position',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)
                    ->where('date', today())
                    ->latest();
    }
}