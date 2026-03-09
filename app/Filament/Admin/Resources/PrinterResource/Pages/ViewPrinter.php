<?php

namespace App\Filament\Admin\Resources\PrinterResource\Pages;

use App\Filament\Admin\Resources\PrinterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrinter extends ViewRecord
{
    protected static string $resource = PrinterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
