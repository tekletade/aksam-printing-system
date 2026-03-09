<?php

namespace App\Filament\Admin\Resources\InventoryTransactionResource\Pages;

use App\Filament\Admin\Resources\InventoryTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventoryTransaction extends ViewRecord
{
    protected static string $resource = InventoryTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
