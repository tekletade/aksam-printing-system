<?php

namespace App\Filament\Admin\Pages;

use App\Models\Attendance;
use App\Models\Employee;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.attendance-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Attendance Report';

    protected static ?int $navigationSort = 1;

    public ?array $filters = [
        'from_date' => null,
        'to_date' => null,
        'employee_id' => null,
        'department' => null,
        'status' => null,
    ];

    public $reportData = null;
    public $summaryStats = null;

    public function mount(): void
    {
        // Set default dates to current month
        $this->filters['from_date'] = now()->startOfMonth()->format('Y-m-d');
        $this->filters['to_date'] = now()->endOfMonth()->format('Y-m-d');
    }

    public function generateReport()
    {
        // Validate filters
        $this->validate([
            'filters.from_date' => 'required|date',
            'filters.to_date' => 'required|date|after_or_equal:filters.from_date',
        ]);

        // Build query
        $query = Attendance::query()
            ->with(['employee', 'shift'])
            ->whereBetween('attendance_date', [
                Carbon::parse($this->filters['from_date']),
                Carbon::parse($this->filters['to_date'])
            ]);

        if ($this->filters['employee_id']) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        if ($this->filters['department']) {
            $query->whereHas('employee', function ($q) {
                $q->where('department', $this->filters['department']);
            });
        }

        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        $this->reportData = $query->get();

        // Calculate summary statistics
        $this->summaryStats = [
            'total_records' => $this->reportData->count(),
            'present' => $this->reportData->where('status', 'present')->count(),
            'absent' => $this->reportData->where('status', 'absent')->count(),
            'late' => $this->reportData->where('is_late', true)->count(),
            'on_leave' => $this->reportData->whereIn('status', ['on_leave', 'leave'])->count(),
            'half_day' => $this->reportData->where('status', 'half_day')->count(),
            'total_hours' => $this->reportData->sum('total_hours'),
            'overtime_hours' => $this->reportData->sum('overtime_hours'),
            'unique_employees' => $this->reportData->unique('employee_id')->count(),
        ];

        Notification::make()
            ->title('Report generated successfully')
            ->success()
            ->send();
    }

    public function exportPdf()
    {
        if (!$this->reportData) {
            Notification::make()
                ->title('Generate the report first')
                ->warning()
                ->send();
            return;
        }

        $data = [
            'reportData' => $this->reportData,
            'summaryStats' => $this->summaryStats,
            'filters' => $this->filters,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('pdf.attendance-report', $data);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'attendance-report-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function exportExcel()
    {
        if (!$this->reportData) {
            Notification::make()
                ->title('Generate the report first')
                ->warning()
                ->send();
            return;
        }

        // You'll need to install: composer require maatwebsite/excel
        // return Excel::download(new AttendanceReportExport($this->reportData), 'attendance-report.xlsx');

        Notification::make()
            ->title('Excel export - Install maatwebsite/excel package')
            ->warning()
            ->send();
    }

    public function getDepartmentOptions()
    {
        return Employee::distinct('department')
            ->pluck('department', 'department')
            ->toArray();
    }

    public function getEmployeeOptions()
    {
        return Employee::query()
            ->when($this->filters['department'], function ($query) {
                $query->where('department', $this->filters['department']);
            })
            ->get()
            ->mapWithKeys(fn ($employee) => [$employee->id => $employee->full_name])
            ->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Filter Attendance Report')
                ->schema([
                    Select::make('filters.employee_id')
                        ->label('Employee')
                        ->options(fn () => $this->getEmployeeOptions())
                        ->searchable()
                        ->placeholder('All Employees'),

                    Select::make('filters.department')
                        ->label('Department')
                        ->options(fn () => $this->getDepartmentOptions())
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('filters.employee_id', null))
                        ->placeholder('All Departments'),

                    Select::make('filters.status')
                        ->options([
                            'present' => 'Present',
                            'absent' => 'Absent',
                            'late' => 'Late',
                            'half_day' => 'Half Day',
                            'on_leave' => 'On Leave',
                        ])
                        ->placeholder('All Statuses'),

                    DatePicker::make('filters.from_date')
                        ->label('From Date')
                        ->required()
                        ->default(now()->startOfMonth()),

                    DatePicker::make('filters.to_date')
                        ->label('To Date')
                        ->required()
                        ->default(now()->endOfMonth()),
                ])->columns(3)
        ];
    }
}
