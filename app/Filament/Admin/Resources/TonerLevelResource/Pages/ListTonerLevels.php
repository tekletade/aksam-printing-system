<?php

namespace App\Filament\Admin\Resources\TonerLevelResource\Pages;

use App\Filament\Admin\Resources\TonerLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTonerLevels extends ListRecords
{
    protected static string $resource = TonerLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
