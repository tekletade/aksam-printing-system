<?php

namespace App\Filament\Admin\Resources\InventoryItemResource\Pages;

use App\Filament\Admin\Resources\InventoryItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventoryItem extends ViewRecord
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
