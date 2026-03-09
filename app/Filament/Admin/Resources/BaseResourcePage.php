<?php

namespace App\Filament\Admin\Resources;

trait BaseResourcePage
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Created successfully';
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Updated successfully';
    }
}
