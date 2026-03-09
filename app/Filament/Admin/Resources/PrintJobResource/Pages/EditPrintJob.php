<?php

namespace App\Filament\Admin\Resources\PrintJobResource\Pages;

use App\Filament\Admin\Resources\PrintJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrintJob extends EditRecord
{
    protected static string $resource = PrintJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
