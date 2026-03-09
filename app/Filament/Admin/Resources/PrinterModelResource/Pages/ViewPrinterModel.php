<?php

namespace App\Filament\Admin\Resources\PrinterModelResource\Pages;

use App\Filament\Admin\Resources\PrinterModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrinterModel extends ViewRecord
{
    protected static string $resource = PrinterModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
