@extends('layouts.admin')

@section('title', 'Manajemen Absensi')

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
        <span class="text-gray-500">Absensi</span>
    </div>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Absensi</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola dan pantau kehadiran karyawan</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="showAddAttendanceModal()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Absensi
            </button>
            <a href="{{ route('admin.export.attendance') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-download mr-2"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayTotal ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Hadir</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayPresent ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Terlambat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayLate ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Alpha</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayAbsent ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percentage text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Kehadiran</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $attendanceRate ?? 0 }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <!-- Date Range -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Mulai
                </label>
                <input type="date" name="start_date" id="start_date" 
                       value="{{ request('start_date', today()->format('Y-m-d')) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Akhir
                </label>
                <input type="date" name="end_date" id="end_date" 
                       value="{{ request('end_date', today()->format('Y-m-d')) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Employee Filter -->
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Karyawan
                </label>
                <select name="employee_id" id="employee_id" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees ?? [] as $emp)
                    <option value="{{ isset($emp) && is_object($emp) ? $emp->id : '' }}" {{ isset($emp) && is_object($emp) && request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->nama ?? 'No Name' }} ({{ $emp->nip ?? 'No Nip'}})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Status
                </label>
                <select name="status" id="status" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                </select>
            </div>

            <!-- Department Filter -->
            <div>
                <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">
                    Jabatan
                </label>
                <select name="jabatan" id="jabatan" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jabatan</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept }}" {{ request('jabatan') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.attendances.index') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    Data Absensi
                    @if(isset($attendances) && $attendances->total() > 0)
                    <span class="text-sm font-normal text-gray-500">
                        ({{ $attendances->total() }} record)
                    </span>
                    @endif
                </h3>
                <div class="flex items-center space-x-2">
                    <button onclick="bulkAction('export')" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-download mr-2"></i>
                        Export Selected
                    </button>
                    <button onclick="bulkAction('delete')" 
                            class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Karyawan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Check In
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Check Out
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Keterlambatan
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances ?? [] as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="attendance-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="{{ $attendance->id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $attendance->tanggal->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $attendance->tanggal->format('l') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    @if($attendance->employee->foto)
                                    <img class="h-8 w-8 rounded-full object-cover" 
                                         src="{{ asset('storage/'.$attendance->employee->foto) }}" 
                                         alt="{{ $attendance->employee->nama }}">
                                    @else
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-xs"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->employee->nama }}</div>
                                    <div class="text-xs text-gray-500">{{ $attendance->employee->nip }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attendance->jam_checkin)
                            <div class="text-sm text-gray-900">{{ $attendance->jam_checkin }}</div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attendance->jam_checkout)
                            <div class="text-sm text-gray-900">{{ $attendance->jam_checkout }}</div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $attendance->status == 'hadir' ? 'bg-green-100 text-green-800' : 
                                   ($attendance->status == 'terlambat' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($attendance->status == 'alpha' ? 'bg-red-100 text-red-800' : 
                                     ($attendance->status == 'izin' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'))) }}">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($attendance->terlambat_menit > 0)
                            <div class="text-sm text-red-600">
                                +{{ $attendance->terlambat_menit }} menit
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button onclick="viewAttendance({{ $attendance->id }})" 
                                        class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editAttendance({{ $attendance->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteAttendance({{ $attendance->id }})" 
                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Tidak ada data absensi</p>
                                <p class="text-sm">Silakan ubah filter atau tambah data absensi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($attendances) && $attendances->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $attendances->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan 
                            <span class="font-medium">{{ $attendances->firstItem() }}</span>
                            sampai
                            <span class="font-medium">{{ $attendances->lastItem() }}</span>
                            dari
                            <span class="font-medium">{{ $attendances->total() }}</span>
                            record
                        </p>
                    </div>
                    <div>
                        {{ $attendances->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Add/Edit Attendance Modal -->
<div id="attendanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Tambah Absensi</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="attendanceForm" class="space-y-4">
                <input type="hidden" id="attendanceId" name="id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                        <select name="employee_id" id="modalEmployeeId" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Karyawan</option>
                            @foreach($employees ?? [] as $emp)
                            <option value="{{ is_object($emp) ? $emp->id : 0 }}">{{ is_object($emp) ? $emp->nama : 'No Name' }} ({{ is_object($emp) ? $emp->nip : 'No Nip' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" id="modalTanggal" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Check In</label>
                        <input type="time" name="jam_checkin" id="modalCheckin"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Check Out</label>
                        <input type="time" name="jam_checkout" id="modalCheckout"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="modalStatus" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="alpha">Alpha</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terlambat (menit)</label>
                        <input type="number" name="terlambat_menit" id="modalTerlambat" min="0"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="modalKeterangan" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Keterangan tambahan..."></textarea>
                </div>
                
                <div class="flex items-center justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                        <i class="fas fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const attendanceCheckboxes = document.querySelectorAll('.attendance-checkbox');
    
    selectAllCheckbox?.addEventListener('change', function() {
        attendanceCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all when individual checkboxes change
    attendanceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.attendance-checkbox:checked');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedBoxes.length === attendanceCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < attendanceCheckboxes.length;
            }
        });
    });

    // Modal functions
    window.showAddAttendanceModal = function() {
        document.getElementById('modalTitle').textContent = 'Tambah Absensi';
        document.getElementById('attendanceForm').reset();
        document.getElementById('attendanceId').value = '';
        document.getElementById('modalTanggal').value = new Date().toISOString().split('T')[0];
        document.getElementById('attendanceModal').classList.remove('hidden');
    };

    window.editAttendance = function(id) {
        // Fetch attendance data and populate form
        fetch(`/admin/attendances/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = 'Edit Absensi';
                document.getElementById('attendanceId').value = data.id;
                document.getElementById('modalEmployeeId').value = data.employee_id;
                document.getElementById('modalTanggal').value = data.tanggal;
                document.getElementById('modalCheckin').value = data.jam_checkin;
                document.getElementById('modalCheckout').value = data.jam_checkout;
                document.getElementById('modalStatus').value = data.status;
                document.getElementById('modalTerlambat').value = data.terlambat_menit;
                document.getElementById('modalKeterangan').value = data.keterangan;
                document.getElementById('attendanceModal').classList.remove('hidden');
            });
    };

    window.viewAttendance = function(id) {
        window.location.href = `/admin/attendances/${id}`;
    };

    window.deleteAttendance = function(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data absensi ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/attendances/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    };

    window.closeModal = function() {
        document.getElementById('attendanceModal').classList.add('hidden');
    };

    // Form submission
    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const id = document.getElementById('attendanceId').value;
        const url = id ? `/admin/attendances/${id}` : '/admin/attendances';
        const method = id ? 'PUT' : 'POST';
        
        if (id) {
            formData.append('_method', 'PUT');
        }
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    });

    // Bulk actions
    window.bulkAction = function(action) {
        const checkedBoxes = document.querySelectorAll('.attendance-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu data absensi');
            return;
        }
        
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (action === 'delete') {
            if (confirm(`Apakah Anda yakin ingin menghapus ${ids.length} data absensi?`)) {
                // Handle bulk delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/attendances/bulk-delete';
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="ids" value="${ids.join(',')}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        } else if (action === 'export') {
            // Handle bulk export
            window.location.href = `/admin/attendances/export?ids=${ids.join(',')}`;
        }
    };

    // Auto-calculate late minutes based on check-in time
    document.getElementById('modalCheckin')?.addEventListener('change', function() {
        const checkinTime = this.value;
        if (checkinTime) {
            const checkin = new Date(`2000-01-01 ${checkinTime}`);
            const workStart = new Date(`2000-01-01 07:00`);
            
            if (checkin > workStart) {
                const lateMinutes = Math.floor((checkin - workStart) / (1000 * 60));
                document.getElementById('modalTerlambat').value = lateMinutes;
                document.getElementById('modalStatus').value = 'terlambat';
            } else {
                document.getElementById('modalTerlambat').value = 0;
                document.getElementById('modalStatus').value = 'hadir';
            }
        }
    });
});
</script>
@endpush