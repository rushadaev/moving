<?php

namespace App\Filament\Resources\LandingServiceResource\Pages;

use App\Filament\Resources\LandingServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingService extends EditRecord
{
    protected static string $resource = LandingServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
