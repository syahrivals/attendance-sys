<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Sistem Absensi</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Additional Styles -->
    @stack('styles')
    
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: all 0.3s ease; }
    </style>
</head>
<body class="h-full" x-data="{ sidebarOpen: false }" x-cloak>
    <div class="min-h-full">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg sidebar-transition"
             :class="{ '-translate-x-full lg:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen }"
             @click.away="sidebarOpen = false">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-4 bg-blue-600">
                <div class="flex items-center">
                    <i class="fas fa-user-clock text-white text-2xl mr-3"></i>
                    <span class="text-white text-xl font-bold">Absensi</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt mr-3 text-lg {{ request()->routeIs('admin.dashboard') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                        Dashboard
                    </a>

                    <!-- Karyawan -->
                    <div x-data="{ open: {{ request()->routeIs('admin.employees.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-users mr-3 text-lg text-gray-400"></i>
                                Karyawan
                            </div>
                            <i class="fas fa-chevron-right transition-transform" :class="{ 'rotate-90': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-1 space-y-1">
                            <a href="{{ route('admin.employees.index') }}" 
                               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.employees.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                <i class="fas fa-list mr-2"></i> Daftar Karyawan
                            </a>
                            <a href="{{ route('admin.employees.create') }}" 
                               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.employees.create') ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                <i class="fas fa-plus mr-2"></i> Tambah Karyawan
                            </a>
                        </div>
                    </div>

                    <!-- Absensi -->
                    <div x-data="{ open: {{ request()->routeIs('admin.attendances.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-check mr-3 text-lg text-gray-400"></i>
                                Absensi
                            </div>
                            <i class="fas fa-chevron-right transition-transform" :class="{ 'rotate-90': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-1 space-y-1">
                            <a href="{{ route('admin.attendances.index') }}" 
                               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.attendances.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                <i class="fas fa-list mr-2"></i> Daftar Absensi
                            </a>
                            <a href="{{ route('admin.attendances.today') }}" 
                               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.attendances.today') ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                <i class="fas fa-clock mr-2"></i> Absensi Hari Ini
                            </a>
                        </div>
                    </div>

                    <!-- Laporan & Export -->
                    <div x-data="{ open: {{ request()->routeIs('admin.reports.*', 'admin.export.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" 
                                class="group w-full flex items-center justify-between px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar mr-3 text-lg text-gray-400"></i>
                                Laporan
                            </div>
                            <i class="fas fa-chevron-right transition-transform" :class="{ 'rotate-90': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="ml-6 mt-1 space-y-1">
                            <a href="{{ route('admin.reports.index') }}" 
                               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.reports.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                <i class="fas fa-file-alt mr-2"></i> Laporan Absensi
                            </a>
                            <a href="{{ route('admin.export.index') }}" 
                               class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.export.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                <i class="fas fa-download mr-2"></i> Export Data
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 my-4"></div>

                    <!-- Pengaturan -->
                    <a href="{{ route('admin.settings') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-cog mr-3 text-lg {{ request()->routeIs('admin.settings') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                        Pengaturan
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('admin.profile') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.profile') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-user mr-3 text-lg {{ request()->routeIs('admin.profile') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                        Profil
                    </a>
                </div>
            </nav>

            <!-- User Info & Logout -->
            <div class="absolute bottom-0 w-full p-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Navigation -->
            <div class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4">
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="lg:hidden text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Breadcrumb -->
                    <nav class="hidden lg:flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm">
                            @yield('breadcrumb')
                        </ol>
                    </nav>

                    <!-- Top Right Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full relative">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute top-0 right-0 block h-2 w-2 bg-red-400 rounded-full"></span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                                <div class="px-4 py-2 border-b">
                                    <h3 class="font-medium text-gray-900">Notifikasi</h3>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                            <div>
                                                <p class="font-medium">Karyawan baru terdaftar</p>
                                                <p class="text-gray-500">5 menit yang lalu</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="hidden lg:block">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down ml-1"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user mr-2"></i> Profil
                                </a>
                                <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-cog mr-2"></i> Pengaturan
                                </a>
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-6">
                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                        <div class="text-sm text-green-700">{{ session('success') }}</div>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
                        <div class="text-sm text-red-700">{{ session('error') }}</div>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-red-400 mr-3 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Main Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" x-transition.opacity 
         class="fixed inset-0 z-40 bg-black bg-opacity-25 lg:hidden" 
         @click="sidebarOpen = false"></div>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
