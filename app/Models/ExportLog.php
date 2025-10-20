<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'format',
        'file_path',
        'file_size',
        'filters',
    ];

    protected $casts = [
        'filters' => 'array',
    ];

    /**
     * Generate a human-readable file size string.
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes <= 0) {
            return '0 KB';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));

        return number_format($bytes / pow(1024, $power), 1) . ' ' . $units[$power];
    }

    /**
     * Build a download url for the stored file.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('admin.export.download', $this);
    }

    /**
     * Delete the underlying file when the model is removed.
     */
    protected static function booted(): void
    {
        static::deleting(function (ExportLog $log) {
            if ($log->file_path && Storage::exists($log->file_path)) {
                Storage::delete($log->file_path);
            }
        });
    }
}

