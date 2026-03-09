<?php

namespace App\Filament\Admin\Resources\PrinterResource\Pages;

use App\Filament\Admin\Resources\PrinterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrinters extends ListRecords
{
    protected static string $resource = PrinterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
