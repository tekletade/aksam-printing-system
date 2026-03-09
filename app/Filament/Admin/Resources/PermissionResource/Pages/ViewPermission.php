<?php

namespace App\Filament\Admin\Resources\PermissionResource\Pages;

use App\Filament\Admin\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPermission extends ViewRecord
{
    protected static string $resource = PermissionResource::class;

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
                Infolists\Components\Section::make('Permission Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('guard_name')
                            ->badge(),
                    ])->columns(2),

                Infolists\Components\Section::make('Roles with this Permission')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('roles')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->badge()
                                    ->color('success'),
                            ])
                            ->grid(3)
                            ->contained(false),
                    ]),
            ]);
    }
}
