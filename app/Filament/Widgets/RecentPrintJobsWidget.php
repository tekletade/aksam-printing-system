<?php

namespace App\Filament\Widgets;

use App\Models\PrintJob;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentPrintJobsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PrintJob::query()
                    ->with(['printer', 'user'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('job_id')
                    ->label('Job ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('printer.name')
                    ->label('Printer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('document_name')
                    ->label('Document')
                    ->limit(30),

                Tables\Columns\TextColumn::make('pages')
                    ->numeric()
                    ->alignRight(),

                Tables\Columns\TextColumn::make('color_mode')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('total_price')
                    ->money('ETB')
                    ->alignRight(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->poll('10s');
    }

    protected function getTableHeading(): string
    {
        return 'Recent Print Jobs';
    }
}
