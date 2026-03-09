<?php

namespace App\Filament\Admin\Resources\PaperInventoryResource\Pages;

use App\Filament\Admin\Resources\PaperInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaperInventory extends EditRecord
{
    protected static string $resource = PaperInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
