<?php

namespace App\Filament\Admin\Resources\EmployeeResource\Pages;

use App\Filament\Admin\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('attendance')
                ->label('View Attendance')
                ->icon('heroicon-o-clock')
                ->url(fn () => route('filament.admin.resources.attendances.index', ['employee_id' => $this->record->id])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Personal Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('employee_id'),
                        Infolists\Components\TextEntry::make('full_name'),
                        Infolists\Components\TextEntry::make('date_of_birth')->date(),
                        Infolists\Components\TextEntry::make('gender'),
                        Infolists\Components\TextEntry::make('marital_status'),
                    ])->columns(3),

                Infolists\Components\Section::make('Contact Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('phone_primary'),
                        Infolists\Components\TextEntry::make('phone_secondary'),
                        Infolists\Components\TextEntry::make('email'),
                    ])->columns(3),

                Infolists\Components\Section::make('Employment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('department'),
                        Infolists\Components\TextEntry::make('position'),
                        Infolists\Components\TextEntry::make('job_title'),
                        Infolists\Components\TextEntry::make('employment_type')->badge(),
                        Infolists\Components\TextEntry::make('hire_date')->date(),
                        Infolists\Components\TextEntry::make('status')->badge(),
                    ])->columns(3),
            ]);
    }
}
