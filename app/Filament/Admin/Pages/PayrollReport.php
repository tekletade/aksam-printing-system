<?php

namespace App\Filament\Admin\Pages;

use App\Models\PayrollRun;
use App\Models\PayrollPeriod;
use App\Models\Employee;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class PayrollReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'filament.admin.pages.payroll-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Payroll Report';

    protected static ?int $navigationSort = 2;

    public ?array $filters = [
        'payroll_period_id' => null,
        'department' => null,
        'employee_id' => null,
        'status' => null,
    ];

    public $reportData = null;
    public $summaryStats = null;

    public function mount(): void
    {
        // Set default to latest payroll period
        $latestPeriod = PayrollPeriod::latest()->first();
        if ($latestPeriod) {
            $this->filters['payroll_period_id'] = $latestPeriod->id;
        }
    }

    public function generateReport()
    {
        $this->validate([
            'filters.payroll_period_id' => 'required|exists:payroll_periods,id',
        ]);

        $query = PayrollRun::query()
            ->with(['employee', 'payrollPeriod'])
            ->where('payroll_period_id', $this->filters['payroll_period_id']);

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
            'total_employees' => $this->reportData->count(),
            'total_gross' => $this->reportData->sum('gross_pay'),
            'total_deductions' => $this->reportData->sum('total_deductions'),
            'total_net' => $this->reportData->sum('net_pay'),
            'total_tax' => $this->reportData->sum('income_tax'),
            'total_pension_employee' => $this->reportData->sum('pension_employee'),
            'total_pension_employer' => $this->reportData->sum('pension_employer'),
            'total_overtime' => $this->reportData->sum('overtime_pay'),
            'total_bonus' => $this->reportData->sum('bonus'),
            'average_net' => $this->reportData->avg('net_pay'),
        ];

        Notification::make()
            ->title('Payroll report generated successfully')
            ->success()
            ->send();
    }

    public function getPayrollPeriodOptions()
    {
        return PayrollPeriod::orderBy('start_date', 'desc')
            ->get()
            ->mapWithKeys(fn ($period) => [
                $period->id => $period->name . ' (' . $period->start_date->format('M d, Y') . ' - ' . $period->end_date->format('M d, Y') . ')'
            ])
            ->toArray();
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
            ->mapWithKeys(fn ($employee) => [$employee->id => $employee->full_name . ' (' . $employee->employee_id . ')'])
            ->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Filter Payroll Report')
                ->schema([
                    Select::make('filters.payroll_period_id')
                        ->label('Payroll Period')
                        ->options(fn () => $this->getPayrollPeriodOptions())
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('filters.employee_id', null)),

                    Select::make('filters.department')
                        ->label('Department')
                        ->options(fn () => $this->getDepartmentOptions())
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('filters.employee_id', null))
                        ->placeholder('All Departments'),

                    Select::make('filters.employee_id')
                        ->label('Employee')
                        ->options(fn () => $this->getEmployeeOptions())
                        ->searchable()
                        ->placeholder('All Employees'),

                    Select::make('filters.status')
                        ->options([
                            'calculated' => 'Calculated',
                            'reviewed' => 'Reviewed',
                            'approved' => 'Approved',
                            'paid' => 'Paid',
                        ])
                        ->placeholder('All Statuses'),
                ])->columns(2)
        ];
    }
}
