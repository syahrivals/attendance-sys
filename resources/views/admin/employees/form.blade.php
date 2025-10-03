@extends('layouts.admin')

@section('title', isset($employee) ? 'Edit Karyawan' : 'Tambah Karyawan')

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
        <a href="{{ route('admin.employees.index') }}" class="text-gray-400 hover:text-gray-500">Karyawan</a>
    </div>
</li>
<li class="flex">
    <div class="flex items-center">
        <i class="fas fa-chevron-right text-gray-300 mx-2"></i>
        <span class="text-gray-500">{{ isset($employee) ? 'Edit' : 'Tambah' }}</span>
    </div>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isset($employee) ? 'Edit Karyawan' : 'Tambah Karyawan' }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                {{ isset($employee) ? 'Update informasi karyawan' : 'Tambah karyawan baru ke sistem' }}
            </p>
        </div>
        <a href="{{ route('admin.employees.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ isset($employee) ? route('admin.employees.update', $employee) : route('admin.employees.store') }}" 
              method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($employee))
                @method('PUT')
            @endif

            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Karyawan</h3>
                <p class="text-sm text-gray-500 mt-1">Lengkapi data karyawan dengan benar</p>
            </div>

            <div class="px-6 space-y-6">
                <!-- Foto Profile -->
                <div class="flex items-start space-x-6">
                    <div class="flex-shrink-0">
                        <div class="h-32 w-32 rounded-lg overflow-hidden bg-gray-100 border-2 border-dashed border-gray-300">
                            @if(isset($employee) && $employee->foto)
                                <img id="photoPreview" src="{{ asset('storage/'.$employee->foto) }}" 
                                     alt="Foto Karyawan" class="h-full w-full object-cover">
                            @else
                                <div id="photoPreview" class="h-full w-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Foto Karyawan
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="file" name="foto" id="foto" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            PNG, JPG, GIF up to 2MB. Recommended: 400x400px
                        </p>
                        @error('foto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NIP -->
                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">
                            NIP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nip" id="nip" required
                               value="{{ old('nip', $employee->nip ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('nip') border-red-500 @enderror">
                        @error('nip')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama" id="nama" required
                               value="{{ old('nama', $employee->nama ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('nama') border-red-500 @enderror">
                        @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $employee->email ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Telepon
                        </label>
                        <input type="text" name="phone" id="phone"
                               value="{{ old('phone', $employee->phone ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-gray-700 mb-1">
                            Jabatan <span class="text-red-500">*</span>
                        </label>
                        <select name="jabatan" id="jabatan" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('jabatan') border-red-500 @enderror">
                            <option value="">Pilih Jabatan</option>
                            <option value="Manager" {{ old('jabatan', $employee->jabatan ?? '') == 'Manager' ? 'selected' : '' }}>Manager</option>
                            <option value="Supervisor" {{ old('jabatan', $employee->jabatan ?? '') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="Staff" {{ old('jabatan', $employee->jabatan ?? '') == 'Staff' ? 'selected' : '' }}>Staff</option>
                            <option value="Admin" {{ old('jabatan', $employee->jabatan ?? '') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Operator" {{ old('jabatan', $employee->jabatan ?? '') == 'Operator' ? 'selected' : '' }}>Operator</option>
                            <option value="Security" {{ old('jabatan', $employee->jabatan ?? '') == 'Security' ? 'selected' : '' }}>Security</option>
                            <option value="Cleaning Service" {{ old('jabatan', $employee->jabatan ?? '') == 'Cleaning Service' ? 'selected' : '' }}>Cleaning Service</option>
                        </select>
                        @error('jabatan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                            <option value="aktif" {{ old('status', $employee->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="non-aktif" {{ old('status', $employee->status ?? '') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Bergabung -->
                    <div>
                        <label for="tanggal_bergabung" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Bergabung
                        </label>
                        <input type="date" name="tanggal_bergabung" id="tanggal_bergabung"
                               value="{{ old('tanggal_bergabung', isset($employee) && $employee->tanggal_bergabung ? $employee->tanggal_bergabung->format('Y-m-d') : '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_bergabung') border-red-500 @enderror">
                        @error('tanggal_bergabung')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat
                    </label>
                    <textarea name="alamat" id="alamat" rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('alamat') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap">{{ old('alamat', $employee->alamat ?? '') }}</textarea>
                    @error('alamat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.employees.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    {{ isset($employee) ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </form>
    </div>

    @if(isset($employee))
    <!-- Additional Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Aksi Tambahan</h3>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">Riwayat Absensi</h4>
                    <p class="text-sm text-gray-500">Lihat riwayat kehadiran karyawan</p>
                </div>
                <a href="{{ route('admin.attendances.index', ['employee' => $employee->id]) }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                    <i class="fas fa-history mr-2"></i>
                    Lihat Riwayat
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Photo preview functionality
    const photoInput = document.getElementById('foto');
    const photoPreview = document.getElementById('photoPreview');
    
    photoInput.addEventListener('change', function() {
        const file = this.files[0];
        
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                this.value = '';
                return;
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.innerHTML = `<img src="${e.target.result}" alt="Photo Preview" class="h-full w-full object-cover">`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Auto-format phone number
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.startsWith('0')) {
                value = '62' + value.substring(1);
            } else if (!value.startsWith('62')) {
                value = '62' + value;
            }
        }
        this.value = value;
    });

    // Generate NIP suggestion
    const nipInput = document.getElementById('nip');
    if (!nipInput.value && nipInput.id !== 'nip' || nipInput.value === '') {
        const now = new Date();
        const year = now.getFullYear().toString().substr(-2);
        const month = ('0' + (now.getMonth() + 1)).slice(-2);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const suggestedNIP = `EMP${year}${month}${random}`;
        
        // Show suggestion
        const nipContainer = nipInput.parentElement;
        const suggestion = document.createElement('p');
        suggestion.className = 'text-xs text-blue-600 mt-1 cursor-pointer';
        suggestion.innerHTML = `💡 Saran NIP: ${suggestedNIP} <span class="underline">Gunakan</span>`;
        suggestion.onclick = function() {
            nipInput.value = suggestedNIP;
            this.remove();
        };
        nipContainer.appendChild(suggestion);
    }

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = ['nip', 'nama', 'jabatan', 'status'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('border-red-500');
                
                // Add error message if not exists
                if (!input.parentElement.querySelector('.text-red-500')) {
                    const error = document.createElement('p');
                    error.className = 'text-red-500 text-xs mt-1';
                    error.textContent = 'Field ini wajib diisi';
                    input.parentElement.appendChild(error);
                }
            } else {
                input.classList.remove('border-red-500');
                const error = input.parentElement.querySelector('.text-red-500');
                if (error && error.textContent === 'Field ini wajib diisi') {
                    error.remove();
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi');
        }
    });
    
    // Real-time validation
    document.querySelectorAll('input[required], select[required]').forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });
    });
});
</script>
@endpush