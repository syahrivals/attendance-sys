<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard'); // pastikan file resources/views/admin/dashboard.blade.php ada
    }
}
