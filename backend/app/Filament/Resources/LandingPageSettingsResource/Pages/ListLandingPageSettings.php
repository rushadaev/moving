<?php

namespace App\Filament\Resources\LandingPageSettingsResource\Pages;

use App\Filament\Resources\LandingPageSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandingPageSettings extends ListRecords
{
    protected static string $resource = LandingPageSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
