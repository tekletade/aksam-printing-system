<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Printer;
use App\Models\PrintJob;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PrinterStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPrinters = Printer::count();
        $onlinePrinters = Printer::whereIn('status', ['Ready', 'Printing'])->count();
        $offlinePrinters = Printer::where('status', 'Offline')->count();

        $lowTonerCount = Printer::whereHas('tonerLevels', function ($query) {
            $query->where('current_level', '<=', 15);
        })->count();

        $todayJobs = PrintJob::whereDate('created_at', Carbon::today())->count();
        $todayRevenue = PrintJob::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total_price');

        $pendingOrders = Order::whereIn('status', ['submitted', 'approved'])->count();

        return [
            Stat::make('Total Printers', $totalPrinters)
                ->description($onlinePrinters . ' online, ' . $offlinePrinters . ' offline')
                ->descriptionIcon('heroicon-m-printer')
                ->color('info')
                ->chart([7, 5, 8, 6, 9, 7, 8]),

            Stat::make('Low Toner', $lowTonerCount)
                ->description('Printers need attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowTonerCount > 0 ? 'danger' : 'success'),

            Stat::make("Today's Jobs", $todayJobs)
                ->description('ETB ' . number_format($todayRevenue, 2) . ' revenue')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Pending Orders', $pendingOrders)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),
        ];
    }
}
