<?php

namespace App\Filament\Resources\LandingReviewResource\Pages;

use App\Filament\Resources\LandingReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingReview extends EditRecord
{
    protected static string $resource = LandingReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
