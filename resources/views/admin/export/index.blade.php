@extends('layouts.admin')

@section('title', 'Export Data')

@section('breadcrumb')
<li class="flex">
    <div class="flex items-center">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500">
            <i class="fas fa-home"></i>
        </a>
    </div>
</li>
<li class="flex">
    <div class="flex items-center">
        <i class="fas fa-chevron-right text-gray-300 mx-2"></i>
        <span class="text-gray-500">Export Data</span>
    </div>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Export Data</h1>
                <p class="text-sm text-gray-600 mt-1">Download laporan absensi dan data karyawan dalam format Excel atau CSV</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Total Record</div>
                <div class="text-2xl font-bold text-blue-600">{{ $totalRecords ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Data Absensi -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Data Absensi</h3>
                        <p class="text-sm text-gray-500">Export laporan kehadiran karyawan</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.export.attendance') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" required
                               value="{{ date('Y-m-01') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" required
                               value="{{ date('Y-m-d') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                        <select name="employee_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                            <option value="">Semua Karyawan</option>
                            @foreach($employees ?? [] as $emp)
                            @if(is_object($emp))
                                <option value="{{ $emp->id ?? 0 }}">{{ $emp->nama ?? 'No Name'}} ({{ $emp->nip ?? 'No Nip'}})</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                            <option value="">Semua Status</option>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="alpha">Alpha</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                </div>

                <!-- Format Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="radio" name="format" value="excel" checked class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Excel (.xlsx)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="format" value="csv" class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">CSV (.csv)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="format" value="pdf" class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">PDF (.pdf)</span>
                        </label>
                    </div>
                </div>

                <!-- Include Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data yang Disertakan</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="employee_info" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Informasi Karyawan (Nama, NIP, Jabatan)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="time_details" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Detail Waktu (Check-in, Check-out)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="late_info" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Informasi Keterlambatan</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="summary" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Ringkasan Statistik</span>
                        </label>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-3 px-4 rounded-md transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Data Absensi
                </button>
            </form>
        </div>

        <!-- Data Karyawan -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Data Karyawan</h3>
                        <p class="text-sm text-gray-500">Export master data karyawan</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.export.employees') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Karyawan</label>
                        <select name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="non-aktif">Non-Aktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <select name="jabatan" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Jabatan</option>
                            @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Format Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="radio" name="format" value="excel" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Excel (.xlsx)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="format" value="csv" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">CSV (.csv)</span>
                        </label>
                    </div>
                </div>

                <!-- Include Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data yang Disertakan</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="basic_info" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Informasi Dasar (NIP, Nama, Email)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="job_info" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Informasi Pekerjaan (Jabatan, Status)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="contact_info" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Informasi Kontak (Phone, Alamat)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="join_date" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Tanggal Bergabung</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="include[]" value="attendance_summary" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Ringkasan Absensi Bulan Ini</span>
                        </label>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-3 px-4 rounded-md transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Data Karyawan
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Export Templates -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Template Export Cepat</h3>
            <p class="text-sm text-gray-500 mt-1">Download laporan dengan template yang sudah diatur sebelumnya</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Daily Report -->
                <a href="{{ route('admin.export.template', 'daily') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Laporan Harian</h4>
                        <p class="text-xs text-gray-500">Absensi hari ini</p>
                    </div>
                </a>

                <!-- Weekly Report -->
                <a href="{{ route('admin.export.template', 'weekly') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-week text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Laporan Mingguan</h4>
                        <p class="text-xs text-gray-500">7 hari terakhir</p>
                    </div>
                </a>

                <!-- Monthly Report -->
                <a href="{{ route('admin.export.template', 'monthly') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Laporan Bulanan</h4>
                        <p class="text-xs text-gray-500">Bulan ini</p>
                    </div>
                </a>

                <!-- Late Report -->
                <a href="{{ route('admin.export.template', 'late') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-gray-900">Laporan Keterlambatan</h4>
                        <p class="text-xs text-gray-500">Karyawan terlambat</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Export History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Riwayat Export</h3>
                <button onclick="clearExportHistory()" 
                        class="text-sm text-red-600 hover:text-red-800">
                    Hapus Riwayat
                </button>
            </div>
        </div>
        <div class="p-6">
            @if(isset($exportHistory) && count($exportHistory) > 0)
            <div class="space-y-4">
                @foreach($exportHistory as $export)
                <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                            @if($export['type'] == 'attendance')
                            <i class="fas fa-calendar-check text-gray-600 text-sm"></i>
                            @else
                            <i class="fas fa-users text-gray-600 text-sm"></i>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $export['name'] }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $export['created_at'] }} • {{ $export['size'] }} • {{ strtoupper($export['format']) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ $export['download_url'] }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                        <button onclick="deleteExport('{{ $export['id'] }}')" 
                                class="text-red-600 hover:text-red-800 text-sm">
                            <i class="fas fa-trash mr-1"></i>
                            Hapus
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-file-export text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500">Belum ada riwayat export</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set date range for different periods
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    // Format date for input
    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }
    
    // Template quick actions
    window.setDateRange = function(period) {
        const startDate = document.querySelector('input[name="start_date"]');
        const endDate = document.querySelector('input[name="end_date"]');
        
        switch(period) {
            case 'today':
                startDate.value = formatDate(today);
                endDate.value = formatDate(today);
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                startDate.value = formatDate(yesterday);
                endDate.value = formatDate(yesterday);
                break;
            case 'week':
                const lastWeek = new Date(today);
                lastWeek.setDate(lastWeek.getDate() - 7);
                startDate.value = formatDate(lastWeek);
                endDate.value = formatDate(today);
                break;
            case 'month':
                startDate.value = formatDate(firstDayOfMonth);
                endDate.value = formatDate(today);
                break;
        }
    };
    
    // Add quick date buttons
    const dateButtons = `
        <div class="flex items-center space-x-2 mt-2">
            <button type="button" onclick="setDateRange('today')" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded">Hari Ini</button>
            <button type="button" onclick="setDateRange('yesterday')" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded">Kemarin</button>
            <button type="button" onclick="setDateRange('week')" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded">7 Hari</button>
            <button type="button" onclick="setDateRange('month')" class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded">Bulan Ini</button>
        </div>
    `;
    
    // Add buttons after date inputs
    const dateInputs = document.querySelectorAll('input[name="end_date"]');
    dateInputs.forEach(input => {
        input.parentElement.parentElement.insertAdjacentHTML('afterend', dateButtons);
    });
    
    // Export progress tracking
    function trackExportProgress(exportId) {
        const progressBar = document.getElementById('exportProgress');
        if (progressBar) {
            progressBar.classList.remove('hidden');
            
            // Simulate progress (in real app, use WebSocket or polling)
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 20;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    setTimeout(() => {
                        progressBar.classList.add('hidden');
                        // Refresh export history
                        location.reload();
                    }, 1000);
                }
                progressBar.style.width = progress + '%';
            }, 500);
        }
    }
    
    // Form submissions with progress tracking
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                
                // Re-enable after 5 seconds (adjust based on your needs)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.innerHTML.replace('Memproses...', 'Export');
                }, 5000);
            }
        });
    });
    
    // Clear export history
    window.clearExportHistory = function() {
        if (confirm('Apakah Anda yakin ingin menghapus semua riwayat export?')) {
            fetch('/admin/export/clear-history', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus riwayat');
                }
            });
        }
    };
    
    // Delete specific export
    window.deleteExport = function(exportId) {
        if (confirm('Apakah Anda yakin ingin menghapus file export ini?')) {
            fetch(`/admin/export/${exportId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus file');
                }
            });
        }
    };
});
</script>

<!-- Progress Bar -->
<div id="exportProgress" class="fixed bottom-4 right-4 bg-white border border-gray-200 rounded-lg shadow-lg p-4 hidden">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-download text-blue-600"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">Mengexport data...</p>
            <div class="w-48 bg-gray-200 rounded-full h-2 mt-1">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>
@endpush