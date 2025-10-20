<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminExportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;

// Landing page
Route::get('/', fn() => view('welcome'));

// Login + Logout (pakai Breeze default)
Route::middleware('web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Profile (auth)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard + Employees (admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/activities', [DashboardController::class, 'activities'])->name('admin.dashboard.activities');
    Route::get('/attendances', [\App\Http\Controllers\AdminAttendanceController::class, 'index'])->name('admin.attendances.index');
    Route::get('/attendances/today', [\App\Http\Controllers\AdminAttendanceController::class, 'index'])->name('admin.attendances.today');
    Route::get('/admin/attendances/export', [\App\Http\Controllers\AdminAttendanceController::class, 'exportPreview'])->name('admin.attendances.export');
    Route::get('/admin/attendances/export/download', [\App\Http\Controllers\AdminAttendanceController::class, 'exportDownload'])->name('admin.attendances.export.download');
    Route::post('/admin/attendances/bulk-delete', [\App\Http\Controllers\AdminAttendanceController::class, 'bulkDelete'])->name('admin.attendances.bulk-delete');
    Route::get('/export', [AdminExportController::class, 'index'])->name('admin.export.index');
    Route::post('/export/attendance', [AdminExportController::class, 'exportAttendance'])->name('admin.export.attendance');
    Route::post('/export/employees', [AdminExportController::class, 'exportEmployees'])->name('admin.export.employees');
    Route::get('/export/template/{type}', [AdminExportController::class, 'downloadTemplate'])->name('admin.export.template');
    Route::get('/export/download/{export}', [AdminExportController::class, 'download'])->name('admin.export.download');
    Route::post('/export/clear-history', [AdminExportController::class, 'clearHistory'])->name('admin.export.clear');
    Route::delete('/export/{export}', [AdminExportController::class, 'destroy'])->name('admin.export.delete');
    Route::get('/settings', function () { return view('admin.settings'); })->name('admin.settings');
    Route::get('/reports', [AdminExportController::class, 'index'])->name('admin.reports.index');
    Route::get('/profile', function () { return view('profile.edit'); })->name('admin.profile');
    Route::resource('employees', EmployeeController::class)->names('admin.employees');
});

// Lupa password
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');

