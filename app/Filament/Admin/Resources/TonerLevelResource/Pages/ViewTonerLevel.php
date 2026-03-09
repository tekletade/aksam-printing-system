<?php

namespace App\Filament\Admin\Resources\TonerLevelResource\Pages;

use App\Filament\Admin\Resources\TonerLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTonerLevel extends ViewRecord
{
    protected static string $resource = TonerLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
