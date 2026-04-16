<?php

namespace App\Filament\Resources\VillaAvailabilityResource\Pages;

use App\Filament\Resources\VillaAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVillaAvailability extends EditRecord
{
    protected static string $resource = VillaAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
