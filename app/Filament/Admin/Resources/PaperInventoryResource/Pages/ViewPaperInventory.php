<?php

namespace App\Filament\Admin\Resources\PaperInventoryResource\Pages;

use App\Filament\Admin\Resources\PaperInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaperInventory extends ViewRecord
{
    protected static string $resource = PaperInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
