<?php

namespace App\Filament\Admin\Resources\TonerLevelResource\Pages;

use App\Filament\Admin\Resources\TonerLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTonerLevel extends EditRecord
{
    protected static string $resource = TonerLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
