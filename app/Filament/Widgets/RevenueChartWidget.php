<?php

namespace App\Filament\Widgets;

use App\Models\PrintJob;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Revenue Overview';

    protected function getData(): array
    {
        $data = collect(range(6, 0))->map(function ($days) {
            $date = Carbon::now()->subDays($days);
            $revenue = PrintJob::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_price');

            return [
                'date' => $date->format('M d'),
                'revenue' => $revenue,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Daily Revenue (ETB)',
                    'data' => $data->pluck('revenue')->toArray(),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
