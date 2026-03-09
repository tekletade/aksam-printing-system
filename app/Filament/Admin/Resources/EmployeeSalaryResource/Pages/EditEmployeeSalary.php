<?php

namespace App\Filament\Admin\Resources\EmployeeSalaryResource\Pages;

use App\Filament\Admin\Resources\EmployeeSalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeSalary extends EditRecord
{
    protected static string $resource = EmployeeSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
