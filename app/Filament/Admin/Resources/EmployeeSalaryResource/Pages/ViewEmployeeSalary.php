<?php

namespace App\Filament\Admin\Resources\EmployeeSalaryResource\Pages;

use App\Filament\Admin\Resources\EmployeeSalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployeeSalary extends ViewRecord
{
    protected static string $resource = EmployeeSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
