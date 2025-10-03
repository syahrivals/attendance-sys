@extends('layouts.admin')

@section('title', 'Detail Karyawan')

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
        <span class="text-gray-500">{{ $employee->nama }}</span>
    </div>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-start justify-between">
        <div class="flex items-center space-x-4">
            <!-- Photo -->
            <div class="h-20 w-20 rounded-full overflow-hidden bg-gray-100">
                @if($employee->foto)
                <img src="{{ asset('storage/'.$employee->foto) }}" 
                     alt="{{ $employee->nama }}" class="h-full w-full object-cover">
                @else
                <div class="h-full w-full flex items-center justify-center">
                    <i class="fas fa-user text-gray-400 text-2xl"></i>
                </div>
                @endif
            </div>
            
            <!-- Info -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $employee->nama }}</h1>
                <div class="flex items-center space-x-4 mt-1">
                    <span class="text-sm text-gray-600">{{ $employee->nip }}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $employee->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ $employee->jabatan }}</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.employees.edit', $employee) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.employees.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Info Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Employee Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Personal</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NIP</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->nip }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jabatan</dt>