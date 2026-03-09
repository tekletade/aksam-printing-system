<?php

namespace App\Filament\Admin\Resources\PrinterModelResource\Pages;

use App\Filament\Admin\Resources\PrinterModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrinterModels extends ListRecords
{
    protected static string $resource = PrinterModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
