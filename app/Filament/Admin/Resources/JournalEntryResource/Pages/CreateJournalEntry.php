<?php

namespace App\Filament\Admin\Resources\JournalEntryResource\Pages;

use App\Filament\Admin\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;
}
