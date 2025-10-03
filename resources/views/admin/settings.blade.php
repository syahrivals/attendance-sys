@extends('layouts.admin')

@section('title', 'Pengaturan')

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
        <span class="text-gray-500">Pengaturan</span>
    </div>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Pengaturan Sistem</h1>
        <p class="text-sm text-gray-600">Konfigurasi dasar aplikasi</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Informasi Aplikasi</h2>
                <dl class="text-sm text-gray-700 space-y-2">
                    <div class="flex justify-between">
                        <dt>Nama Aplikasi</dt>
                        <dd>{{ config('app.name') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Environment</dt>
                        <dd>{{ app()->environment() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Versi Laravel</dt>
                        <dd>{{ app()->version() }}</dd>
                    </div>
                </dl>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Aksi Cepat</h2>
                <div class="space-y-2">
                    <form method="POST" action="#">
                        @csrf
                        <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md" disabled>
                            Simpan Perubahan
                        </button>
                        <p class="text-xs text-gray-500 mt-1">Form pengaturan detail bisa ditambahkan nanti.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



