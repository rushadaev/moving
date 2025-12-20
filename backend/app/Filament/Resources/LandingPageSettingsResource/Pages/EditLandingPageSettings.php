<?php

namespace App\Filament\Resources\LandingPageSettingsResource\Pages;

use App\Filament\Resources\LandingPageSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingPageSettings extends EditRecord
{
    protected static string $resource = LandingPageSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
