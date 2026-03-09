<?php

namespace App\Filament\Admin\Resources\PayrollRunResource\Pages;

use App\Filament\Admin\Resources\PayrollRunResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayrollRun extends EditRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
