@php
    /** @var \Illuminate\Support\Collection|array $recentActivities */
    $recentActivities = collect($recentActivities ?? []);
@endphp

@forelse($recentActivities as $activity)
    @php
        $status = strtolower($activity->status ?? '');
        $iconClass = 'fas fa-clock text-yellow-600';
        $statusLabel = 'Aktivitas';

        if ($status === 'hadir') {
            $iconClass = 'fas fa-sign-in-alt text-green-600';
            $statusLabel = 'Check-in';
        } elseif ($status === 'checkout') {
            $iconClass = 'fas fa-sign-out-alt text-blue-600';
            $statusLabel = 'Check-out';
        } elseif ($status === 'terlambat') {
            $iconClass = 'fas fa-clock text-yellow-600';
            $statusLabel = 'Terlambat';
        } elseif ($status === 'tidak hadir') {
            $iconClass = 'fas fa-user-times text-red-600';
            $statusLabel = 'Tidak hadir';
        }

        $time = $activity->check_in_time
            ?? $activity->check_out_time
            ?? optional($activity->created_at)->format('H:i');
    @endphp

    <div class="flex items-center space-x-4">
        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
            <i class="{{ $iconClass }}"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">
                {{ optional($activity->employee)->name ?? 'Unknown Employee' }}
            </p>
            <p class="text-sm text-gray-500">
                {{ $statusLabel }} {{ $time ?? '-' }}
            </p>
        </div>
        <div class="text-sm text-gray-400">
            {{ optional($activity->created_at)->diffForHumans() ?? 'Just now' }}
        </div>
    </div>
@empty
    <div class="text-center py-8">
        <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-4"></i>
        <p class="text-gray-500">Belum ada aktivitas hari ini</p>
    </div>
@endforelse
