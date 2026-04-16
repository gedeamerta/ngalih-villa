<?php

namespace App\Filament\Resources\VillaAvailabilityResource\Pages;

use App\Filament\Resources\VillaAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVillaAvailabilities extends ListRecords
{
    protected static string $resource = VillaAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
