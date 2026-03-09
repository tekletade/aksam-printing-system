<?php

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

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
                Infolists\Components\Section::make('Role Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('guard_name')
                            ->badge(),
                    ])->columns(2),

                Infolists\Components\Section::make('Permissions')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('permissions')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->badge()
                                    ->color('success'),
                            ])
                            ->grid(3)
                            ->contained(false),
                    ]),

                Infolists\Components\Section::make('Users with this Role')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('users')
                            ->schema([
                                Infolists\Components\TextEntry::make('name'),
                                Infolists\Components\TextEntry::make('email'),
                            ])
                            ->grid(2)
                            ->contained(false),
                    ]),
            ]);
    }
}
