<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Machine Management';
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PrinterStatusOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\RecentPrintJobsWidget::class,
            \App\Filament\Widgets\RevenueChartWidget::class,
        ];
    }
}
