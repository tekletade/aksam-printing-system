<?php

namespace App\Filament\Admin\Resources\PayrollPeriodResource\Pages;

use App\Filament\Admin\Resources\PayrollPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayrollPeriods extends ListRecords
{
    protected static string $resource = PayrollPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
