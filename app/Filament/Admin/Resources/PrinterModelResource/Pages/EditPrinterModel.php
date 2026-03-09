<?php

namespace App\Filament\Admin\Resources\PrinterModelResource\Pages;

use App\Filament\Admin\Resources\PrinterModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrinterModel extends EditRecord
{
    protected static string $resource = PrinterModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
