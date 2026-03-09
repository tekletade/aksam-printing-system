<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\ImageEntry::make('avatar')
                            ->circular()
                            ->defaultImageUrl(url('https://ui-avatars.com/api/?name=' . urlencode($this->record->name) . '&color=7F9CF5&background=EBF4FF')),
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('email'),
                        Infolists\Components\TextEntry::make('phone'),
                    ])->columns(2),

                Infolists\Components\Section::make('Roles & Permissions')
                    ->schema([
                        Infolists\Components\TextEntry::make('roles')
                            ->getStateUsing(fn ($record) => $record->roles->pluck('name')->join(', '))
                            ->badge()
                            ->color('primary'),

                        Infolists\Components\TextEntry::make('permissions')
                            ->getStateUsing(fn ($record) => $record->getAllPermissions()->pluck('name')->join(', '))
                            ->badge()
                            ->color('success'),
                    ]),

                Infolists\Components\Section::make('Account Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Unverified')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),

                        Infolists\Components\TextEntry::make('is_active')
                            ->label('Status')
                            ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('Linked Records')
                    ->schema([
                        Infolists\Components\TextEntry::make('employee.full_name')
                            ->label('Employee Profile')
                            ->url(fn ($record) => $record->employee ? route('filament.admin.resources.employees.view', $record->employee) : null),

                        Infolists\Components\TextEntry::make('customer.name')
                            ->label('Customer Profile')
                            ->url(fn ($record) => $record->customer ? route('filament.admin.resources.customers.view', $record->customer) : null),
                    ])->columns(2),
            ]);
    }
}
