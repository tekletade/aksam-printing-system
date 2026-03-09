<?php

namespace App\Filament\Admin\Resources\PrinterResource\Pages;

use App\Filament\Admin\Resources\PrinterResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrinter extends CreateRecord
{
    protected static string $resource = PrinterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
