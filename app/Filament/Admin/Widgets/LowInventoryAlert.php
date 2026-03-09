<?php

namespace App\Filament\Admin\Widgets;

use App\Models\InventoryItem;
use App\Models\Printer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowInventoryAlert extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Low Inventory Alerts';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Combine low toner and low paper alerts
                $this->getLowInventoryQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'toner' => 'danger',
                        'paper' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('printer_name')
                    ->label('Printer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('item_name')
                    ->label('Item')
                    ->searchable(),

                Tables\Columns\TextColumn::make('current_level')
                    ->label('Current Level')
                    ->numeric()
                    ->suffix(fn ($record) => $record->type === 'toner' ? '%' : ' sheets')
                    ->badge()
                    ->color(fn ($record): string =>
                        $record->current_level <= $record->threshold_critical ? 'danger' : 'warning'
                    ),

                Tables\Columns\TextColumn::make('threshold')
                    ->label('Threshold')
                    ->getStateUsing(fn ($record) =>
                        $record->type === 'toner'
                            ? $record->threshold_warning . '%'
                            : $record->threshold_reorder . ' sheets'
                    ),

                Tables\Columns\TextColumn::make('last_updated')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record): string =>
                        $record->current_level <= $record->threshold_critical ? 'danger' : 'warning'
                    )
                    ->getStateUsing(fn ($record) =>
                        $record->current_level <= $record->threshold_critical ? 'Critical' : 'Low'
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('view_printer')
                    ->label('View Printer')
                    ->icon('heroicon-m-eye')
                    ->url(fn ($record) => route('filament.admin.resources.printers.view', $record->printer_id)),
            ])
            ->poll('30s')
            ->defaultSort('current_level', 'asc');
    }

    protected function getLowInventoryQuery(): Builder
    {
        // This is a union query of toner and paper alerts
        // For now, return an empty query if the tables don't exist yet
        $tonerQuery = \App\Models\TonerLevel::query()
            ->whereColumn('current_level', '<=', 'threshold_warning')
            ->selectRaw("
                'toner' as type,
                printer_id,
                toner_color as item_name,
                current_level,
                threshold_warning,
                threshold_critical,
                created_at as last_updated,
                printer_id
            ");

        $paperQuery = \App\Models\PaperInventory::query()
            ->whereColumn('current_sheets', '<=', 'threshold_reorder')
            ->selectRaw("
                'paper' as type,
                printer_id,
                CONCAT(paper_size, ' - ', tray_name) as item_name,
                current_sheets as current_level,
                threshold_reorder as threshold_warning,
                threshold_critical,
                updated_at as last_updated,
                printer_id
            ");

        // Union the queries
        return $tonerQuery->union($paperQuery)
            ->orderBy('current_level')
            ->limit(20);
    }

    public static function canView(): bool
    {
        // Only show if there are low inventory items
        return \App\Models\TonerLevel::whereColumn('current_level', '<=', 'threshold_warning')->exists() ||
               \App\Models\PaperInventory::whereColumn('current_sheets', '<=', 'threshold_reorder')->exists();
    }


public static function getNavigationBadge(): ?string
{
    return \App\Models\InventoryItem::whereColumn('current_stock', '<=', 'minimum_stock')->count();
}
}
