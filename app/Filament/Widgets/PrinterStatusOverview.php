<?php

namespace App\Filament\Widgets;

use App\Models\Printer;
use App\Models\PrintJob;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PrinterStatusOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPrinters = Printer::count();
        $onlinePrinters = Printer::whereIn('status', ['Ready', 'Printing'])->count();
        $offlinePrinters = Printer::where('status', 'Offline')->count();
        $errorPrinters = Printer::whereIn('status', ['Error', 'Paper Jam'])->count();

        $lowTonerCount = Printer::whereHas('tonerLevels', function ($query) {
            $query->whereRaw('current_level <= threshold_warning');
        })->count();

        $todayJobs = PrintJob::whereDate('created_at', Carbon::today())->count();
        $todayRevenue = PrintJob::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total_price');

        $presentToday = Employee::whereHas('attendances', function ($query) {
            $query->whereDate('attendance_date', Carbon::today())
                ->where('status', 'present');
        })->count();

        $totalEmployees = Employee::where('status', 'active')->count();
        $attendanceRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100) : 0;

        return [
            Stat::make('Printers', $totalPrinters)
                ->description($onlinePrinters . ' online, ' . $offlinePrinters . ' offline')
                ->descriptionIcon('heroicon-m-printer')
                ->color('info')
                ->chart([7, 5, 8, 6, 9, 7, 8]),

            Stat::make('Low Toner', $lowTonerCount)
                ->description('Printers need attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowTonerCount > 0 ? 'danger' : 'success')
                ->chart([2, 3, 1, 4, 2, 3, 2]),

            Stat::make("Today's Jobs", $todayJobs)
                ->description(number_format($todayRevenue, 2) . ' ETB revenue')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Attendance', $presentToday . '/' . $totalEmployees)
                ->description($attendanceRate . '% present today')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }
}
