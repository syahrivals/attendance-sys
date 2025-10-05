@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-sm text-gray-600">Selamat datang kembali, {{ auth()->user()->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-lg font-semibold text-blue-600" id="current-time"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Karyawan -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEmployees ?? 0 }}</p>
                    <p class="text-xs text-green-600">
                        <i class="fas fa-arrow-up"></i> {{ $activeEmployees ?? 0 }} Aktif
                    </p>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Hadir Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayPresent ?? 0 }}</p>
                    <p class="text-xs text-gray-500">
                        dari {{ $totalEmployees ?? 0 }} karyawan
                    </p>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Terlambat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayLate ?? 0 }}</p>
                    <p class="text-xs text-yellow-600">
                        <i class="fas fa-exclamation-triangle"></i> Perlu perhatian
                    </p>
                </div>
            </div>
        </div>

        <!-- Alpha/Tidak Hadir -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-times text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Alpha</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayAbsent ?? 0 }}</p>
                    <p class="text-xs text-red-600">
                        <i class="fas fa-times-circle"></i> Tidak hadir
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Grafik Absensi Minggu Ini -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Absensi Minggu Ini</h3>
                <div class="flex space-x-2">
                    <button class="text-sm px-3 py-1 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition-colors" onclick="changeChartPeriod('week')">
                        Minggu
                    </button>
                    <button class="text-sm px-3 py-1 text-gray-600 rounded-md hover:bg-gray-100 transition-colors" onclick="changeChartPeriod('month')">
                        Bulan
                    </button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.employees.create') }}" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">Tambah Karyawan</p>
                        <p class="text-sm text-gray-500">Daftarkan karyawan baru</p>
                    </div>
                </a>

                <a href="{{ route('admin.attendances.index') }}" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">Kelola Absensi</p>
                        <p class="text-sm text-gray-500">Lihat dan kelola absensi</p>
                    </div>
                </a>

                <a href="{{ route('admin.export.index') }}" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-download text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">Export Data</p>
                        <p class="text-sm text-gray-500">Download laporan Excel/CSV</p>
                    </div>
                </a>

                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cog text-gray-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">Pengaturan</p>
                        <p class="text-sm text-gray-500">Konfigurasi sistem</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.attendances.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat Semua
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4" id="recent-activities">
                @include('admin.dashboard._recent-activities', ['recentActivities' => $recentActivities ?? collect()])
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID');
        document.getElementById('current-time').textContent = timeString;
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Chart
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    let attendanceChart;

    function createChart(period = 'week') {
        if (attendanceChart) {
            attendanceChart.destroy();
        }

        const data = period === 'week' ? {
            labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            datasets: [{
                label: 'Hadir',
                data: [{{ implode(',', $weeklyAttendance['present'] ?? [0,0,0,0,0,0,0]) }}],
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 2
            }, {
                label: 'Terlambat',
                data: [{{ implode(',', $weeklyAttendance['late'] ?? [0,0,0,0,0,0,0]) }}],
                backgroundColor: 'rgba(234, 179, 8, 0.5)',
                borderColor: 'rgb(234, 179, 8)',
                borderWidth: 2
            }, {
                label: 'Alpha',
                data: [{{ implode(',', $weeklyAttendance['absent'] ?? [0,0,0,0,0,0,0]) }}],
                backgroundColor: 'rgba(239, 68, 68, 0.5)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 2
            }]
        } : {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Hadir',
                data: [85, 92, 78, 88],
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 2
            }, {
                label: 'Terlambat',
                data: [12, 8, 15, 10],
                backgroundColor: 'rgba(234, 179, 8, 0.5)',
                borderColor: 'rgb(234, 179, 8)',
                borderWidth: 2
            }, {
                label: 'Alpha',
                data: [3, 0, 7, 2],
                backgroundColor: 'rgba(239, 68, 68, 0.5)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 2
            }]
        };

        attendanceChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    }
                }
            }
        });
    }

    createChart();

    // Global function for chart period change
    window.changeChartPeriod = function(period) {
        // Update button styles
        document.querySelectorAll('[onclick^="changeChartPeriod"]').forEach(btn => {
            btn.classList.remove('bg-blue-100', 'text-blue-600');
            btn.classList.add('text-gray-600');
        });
        event.target.classList.remove('text-gray-600');
        event.target.classList.add('bg-blue-100', 'text-blue-600');
        
        createChart(period);
    };

    // Auto-refresh recent activities every 30 seconds
    setInterval(function() {
        fetch('{{ route('admin.dashboard.activities') }}')
            .then(response => response.text())
            .then(html => {
                document.getElementById('recent-activities').innerHTML = html;
            })
            .catch(error => console.log('Error refreshing activities:', error));
    }, 30000);
});
</script>
@endsection

