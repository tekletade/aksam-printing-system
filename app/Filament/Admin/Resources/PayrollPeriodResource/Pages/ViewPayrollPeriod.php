<?php

namespace App\Filament\Admin\Resources\PayrollPeriodResource\Pages;

use App\Filament\Admin\Resources\PayrollPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayrollPeriod extends ViewRecord
{
    protected static string $resource = PayrollPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('process_payroll')
                ->label('Process Payroll')
                ->icon('heroicon-o-calculator')
                ->color('success'),
        ];
    }
}
