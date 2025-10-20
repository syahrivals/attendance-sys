<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ExportLog;
use App\Support\SimpleXlsxExporter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExportController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get(['id', 'name', 'employee_id', 'department', 'position']);

        $positions = Employee::query()
            ->whereNotNull('position')
            ->distinct()
            ->orderBy('position')
            ->pluck('position')
            ->values();

        $totalRecords = Attendance::count() + Employee::count();

        $exportHistory = ExportLog::latest()->take(10)->get();

        return view('admin.export.index', [
            'employees' => $employees,
            'positions' => $positions,
            'totalRecords' => $totalRecords,
            'exportHistory' => $exportHistory,
        ]);
    }

    public function exportAttendance(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'status' => ['nullable', Rule::in(['Hadir', 'Checkout', 'Tidak Hadir', 'Terlambat'])],
            'format' => ['required', Rule::in(['excel', 'csv'])],
            'include' => ['array'],
            'include.*' => ['string'],
        ]);

        $query = Attendance::with('employee')
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']]);

        if (! empty($validated['employee_id'])) {
            $query->where('employee_id', $validated['employee_id']);
        }

        if (! empty($validated['status'])) {
            if ($validated['status'] === 'Terlambat') {
                $query->where('late_minutes', '>', 0);
            } else {
                $query->where('status', $validated['status']);
            }
        }

        $attendances = $query
            ->orderBy('date')
            ->orderBy('employee_id')
            ->get();

        if ($attendances->isEmpty()) {
            return back()->with('error', 'Tidak ada data absensi yang memenuhi filter.');
        }

        $include = collect($validated['include'] ?? []);

        $rows = $this->buildAttendanceRows($attendances, $include);

        $filenameBase = 'attendance-' . Carbon::parse($validated['start_date'])->format('Ymd')
            . '-' . Carbon::parse($validated['end_date'])->format('Ymd')
            . '-' . now()->format('His');

        $filters = Arr::only($validated, ['start_date', 'end_date', 'employee_id', 'status', 'format', 'include']);

        session()->flash('success', 'Export data absensi berhasil dibuat.');

        return $this->storeAndDownload('attendance', $validated['format'], $filenameBase, $rows, $filters, 'Absensi');
    }

    public function exportEmployees(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['aktif', 'non-aktif'])],
            'position' => ['nullable', 'string'],
            'format' => ['required', Rule::in(['excel', 'csv'])],
            'include' => ['array'],
            'include.*' => ['string'],
        ]);

        $query = Employee::query()->orderBy('name');

        if (! empty($validated['status'])) {
            $query->where('is_active', $validated['status'] === 'aktif');
        }

        if (! empty($validated['position'])) {
            $query->where('position', $validated['position']);
        }

        $employees = $query->withCount(['attendances as month_presence_count' => function ($q) {
            $q->whereBetween('date', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString(),
            ])->whereIn('status', ['Hadir', 'Checkout']);
        }])->get();

        if ($employees->isEmpty()) {
            return back()->with('error', 'Tidak ada data karyawan yang memenuhi filter.');
        }

        $include = collect($validated['include'] ?? []);

        $rows = $this->buildEmployeeRows($employees, $include);

        $filenameBase = 'employees-' . now()->format('Ymd_His');

        $filters = Arr::only($validated, ['status', 'position', 'format', 'include']);

        session()->flash('success', 'Export data karyawan berhasil dibuat.');

        return $this->storeAndDownload('employees', $validated['format'], $filenameBase, $rows, $filters, 'Karyawan');
    }

    public function downloadTemplate(string $type)
    {
        $type = strtolower($type);
        $today = Carbon::today();

        $requestData = [
            'format' => 'excel',
            'include' => ['employee_info', 'time_details', 'late_info'],
        ];

        switch ($type) {
            case 'daily':
                $requestData['start_date'] = $today->toDateString();
                $requestData['end_date'] = $today->toDateString();
                break;
            case 'weekly':
                $requestData['start_date'] = $today->copy()->subDays(6)->toDateString();
                $requestData['end_date'] = $today->toDateString();
                break;
            case 'monthly':
                $requestData['start_date'] = $today->copy()->startOfMonth()->toDateString();
                $requestData['end_date'] = $today->copy()->endOfMonth()->toDateString();
                break;
            case 'late':
                $requestData['start_date'] = $today->copy()->subDays(6)->toDateString();
                $requestData['end_date'] = $today->toDateString();
                $requestData['status'] = 'Terlambat';
                break;
            default:
                return back()->with('error', 'Template export tidak dikenali.');
        }

        $request = new Request($requestData);
        $request->setMethod('POST');

        return $this->exportAttendance($request);
    }

    public function download(ExportLog $export)
    {
        if (! Storage::exists($export->file_path)) {
            return back()->with('error', 'File export tidak ditemukan.');
        }

        return Storage::download($export->file_path, $export->name, $this->headersForFormat($export->format));
    }

    public function clearHistory()
    {
        ExportLog::query()->each(function (ExportLog $log) {
            $log->delete();
        });

        return response()->json(['success' => true]);
    }

    public function destroy(ExportLog $export)
    {
        $export->delete();

        return response()->json(['success' => true]);
    }

    /**
     * @param  Collection<int, Attendance>  $attendances
     * @param  Collection<int, string>  $include
     */
    protected function buildAttendanceRows(Collection $attendances, Collection $include): array
    {
        $headers = ['Tanggal'];
        $includeEmployee = $include->contains('employee_info');
        $includeTime = $include->contains('time_details');
        $includeLate = $include->contains('late_info');
        $includeSummary = $include->contains('summary');

        if ($includeEmployee) {
            $headers[] = 'NIP';
            $headers[] = 'Nama';
            $headers[] = 'Departemen';
            $headers[] = 'Posisi';
        }

        $headers[] = 'Status';

        if ($includeTime) {
            $headers[] = 'Check In';
            $headers[] = 'Check Out';
        }

        if ($includeLate) {
            $headers[] = 'Terlambat (menit)';
            $headers[] = 'Keterangan Terlambat';
        }

        $headers[] = 'Catatan';

        $rows = [$headers];

        foreach ($attendances as $attendance) {
            $row = [
                optional($attendance->date)->format('Y-m-d'),
            ];

            if ($includeEmployee) {
                $row[] = optional($attendance->employee)->employee_id;
                $row[] = optional($attendance->employee)->name;
                $row[] = optional($attendance->employee)->department;
                $row[] = optional($attendance->employee)->position;
            }

            $row[] = $attendance->status;

            if ($includeTime) {
                $row[] = $attendance->check_in_time;
                $row[] = $attendance->check_out_time;
            }

            if ($includeLate) {
                $row[] = $attendance->late_minutes;
                $row[] = $attendance->late_description;
            }

            $row[] = $attendance->notes;

            $rows[] = $row;
        }

        if ($includeSummary) {
            $rows[] = $this->padRow([], count($headers));

            $summaryRows = $this->attendanceSummaryRows($attendances);

            foreach ($summaryRows as $summaryRow) {
                $rows[] = $this->padRow($summaryRow, count($headers));
            }
        }

        return $rows;
    }

    /**
     * @param  Collection<int, Employee>  $employees
     * @param  Collection<int, string>  $include
     */
    protected function buildEmployeeRows(Collection $employees, Collection $include): array
    {
        $headers = ['NIP', 'Nama'];

        $includeJob = $include->contains('job_info');
        $includeStatus = true; // always include status for clarity
        $includeJoin = $include->contains('join_date');
        $includeAttendance = $include->contains('attendance_summary');

        if ($includeJob) {
            $headers[] = 'Departemen';
            $headers[] = 'Posisi';
        }

        if ($includeStatus) {
            $headers[] = 'Status';
        }

        if ($includeJoin) {
            $headers[] = 'Tanggal Dibuat';
        }

        if ($includeAttendance) {
            $headers[] = 'Absensi Bulan Ini';
        }

        $rows = [$headers];

        foreach ($employees as $employee) {
            $row = [
                $employee->employee_id,
                $employee->name,
            ];

            if ($includeJob) {
                $row[] = $employee->department;
                $row[] = $employee->position;
            }

            if ($includeStatus) {
                $row[] = $employee->is_active ? 'Aktif' : 'Tidak Aktif';
            }

            if ($includeJoin) {
                $row[] = optional($employee->created_at)->format('Y-m-d');
            }

            if ($includeAttendance) {
                $row[] = $employee->month_presence_count ?? 0;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    protected function attendanceSummaryRows(Collection $attendances): array
    {
        $byStatus = $attendances->groupBy('status')->map->count();
        $total = $attendances->count();

        return [
            ['Ringkasan', 'Nilai'],
            ['Total Data', $total],
            ['Hadir', $byStatus->get('Hadir', 0)],
            ['Checkout', $byStatus->get('Checkout', 0)],
            ['Tidak Hadir', $byStatus->get('Tidak Hadir', 0)],
            ['Rata-rata Terlambat (menit)', round((float) ($attendances->avg('late_minutes') ?? 0), 1)],
        ];
    }

    /**
     * @param  array<int, array<int, mixed>>  $rows
     */
    protected function storeAndDownload(string $type, string $format, string $filenameBase, array $rows, array $filters, string $sheetName): StreamedResponse
    {
        [$contents, $extension, $mime] = $format === 'excel'
            ? [SimpleXlsxExporter::make($rows, $sheetName), 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            : [$this->makeCsv($rows), 'csv', 'text/csv'];

        $filename = $filenameBase . '.' . $extension;
        $path = "exports/{$type}/{$filename}";

        Storage::put($path, $contents);

        $log = ExportLog::create([
            'type' => $type,
            'name' => $filename,
            'format' => $format,
            'file_path' => $path,
            'file_size' => Storage::size($path),
            'filters' => $filters,
        ]);

        return Storage::download($log->file_path, $log->name, ['Content-Type' => $mime]);
    }

    /**
     * Create CSV content from row data.
     *
     * @param  array<int, array<int, mixed>>  $rows
     */
    protected function makeCsv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($handle, array_map(static fn ($value) => $value ?? '', $row));
        }

        rewind($handle);

        $csv = stream_get_contents($handle);
        fclose($handle);

        if ($csv === false) {
            throw new \RuntimeException('Gagal membuat konten CSV.');
        }

        return $csv;
    }

    /**
     * Pad a row to match header length.
     *
     * @param  array<int, mixed>  $row
     */
    protected function padRow(array $row, int $totalColumns): array
    {
        return array_pad($row, $totalColumns, '');
    }

    protected function headersForFormat(string $format): array
    {
        return [
            'Content-Type' => $format === 'excel'
                ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                : 'text/csv',
        ];
    }
}
