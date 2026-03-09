<?php

namespace App\Filament\Admin\Resources\ChartOfAccountResource\Pages;

use App\Filament\Admin\Resources\ChartOfAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewChartOfAccount extends ViewRecord
{
    protected static string $resource = ChartOfAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
