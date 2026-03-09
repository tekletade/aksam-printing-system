<?php

namespace App\Filament\Admin\Resources\PrintJobResource\Pages;

use App\Filament\Admin\Resources\PrintJobResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrintJob extends CreateRecord
{
    protected static string $resource = PrintJobResource::class;
}
