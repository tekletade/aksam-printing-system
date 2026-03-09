<?php

namespace App\Filament\Admin\Resources\PayrollRunResource\Pages;

use App\Filament\Admin\Resources\PayrollRunResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('generate_payslip')
                ->label('Generate Payslip')
                ->icon('heroicon-o-document-text'),
        ];
    }
}
