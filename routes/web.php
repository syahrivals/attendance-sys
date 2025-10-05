<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/export', function () { return view('admin.export.index'); })->name('admin.export.index');
    Route::post('/export/attendance', function () { return back()->with('success', 'Export absensi diproses'); })->name('admin.export.attendance');
    Route::post('/export/employees', function () { return back()->with('success', 'Export karyawan diproses'); })->name('admin.export.employees');
    Route::get('/export/template/{type}', function ($type) { return back()->with('success', "Template $type diproses"); })->name('admin.export.template');
    Route::post('/export/clear-history', function () { return response()->json(['success' => true]); })->name('admin.export.clear');
    Route::delete('/export/{id}', function ($id) { return response()->json(['success' => true]); })->name('admin.export.delete');
    Route::get('/settings', function () { return view('admin.settings'); })->name('admin.settings');
    Route::get('/reports', function () { return view('admin.export.index'); })->name('admin.reports.index');
    Route::get('/profile', function () { return view('profile.edit'); })->name('admin.profile');
    Route::resource('employees', EmployeeController::class)->names('admin.employees');
});

// Lupa password
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');
