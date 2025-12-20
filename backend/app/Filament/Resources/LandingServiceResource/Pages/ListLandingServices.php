<?php

namespace App\Filament\Resources\LandingServiceResource\Pages;

use App\Filament\Resources\LandingServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandingServices extends ListRecords
{
    protected static string $resource = LandingServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
