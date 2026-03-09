<?php

namespace App\Filament\Admin\Resources\PrinterModelResource\Pages;

use App\Filament\Admin\Resources\PrinterModelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrinterModel extends CreateRecord
{
    protected static string $resource = PrinterModelResource::class;
}
