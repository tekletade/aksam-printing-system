<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\PayrollRun;
use App\Models\Order;
use App\Models\InventoryTransaction;
use Carbon\Carbon;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.admin.pages.reports';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return 'Reports Dashboard';
    }

    // Summary statistics
    public function getAttendanceSummary()
    {
        return [
            'total_employees' => \App\Models\Employee::where('status', 'active')->count(),
            'present_today' => Attendance::whereDate('attendance_date', Carbon::today())
                ->where('status', 'present')->count(),
            'absent_today' => Attendance::whereDate('attendance_date', Carbon::today())
                ->where('status', 'absent')->count(),
            'on_leave' => Attendance::whereDate('attendance_date', Carbon::today())
                ->where('status', 'on_leave')->count(),
        ];
    }

    public function getPayrollSummary()
    {
        $currentMonth = PayrollRun::whereHas('payrollPeriod', function ($query) {
            $query->whereYear('end_date', Carbon::now()->year)
                ->whereMonth('end_date', Carbon::now()->month);
        });

        return [
            'total_gross' => $currentMonth->sum('gross_pay'),
            'total_net' => $currentMonth->sum('net_pay'),
            'total_tax' => $currentMonth->sum('income_tax'),
            'total_pension' => $currentMonth->sum('pension_employee'),
            'employee_count' => $currentMonth->count(),
        ];
    }

    public function getOrderSummary()
    {
        return [
            'total_orders' => Order::whereMonth('created_at', Carbon::now()->month)->count(),
            'completed_orders' => Order::whereMonth('created_at', Carbon::now()->month)
                ->where('status', 'completed')->count(),
            'pending_orders' => Order::whereIn('status', ['submitted', 'approved', 'processing'])->count(),
            'total_revenue' => Order::whereMonth('created_at', Carbon::now()->month)
                ->where('payment_status', 'paid')->sum('total'),
        ];
    }

    public function getInventorySummary()
    {
        return [
            'low_stock_items' => \App\Models\InventoryItem::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'total_items' => \App\Models\InventoryItem::count(),
            'recent_transactions' => InventoryTransaction::whereDate('created_at', Carbon::today())->count(),
        ];
    }
}
