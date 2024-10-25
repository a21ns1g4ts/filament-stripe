<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Resources\FeatureResource\Pages;

use A21ns1g4ts\FilamentStripe\Filament\Resources\FeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeatures extends ListRecords
{
    protected static string $resource = FeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
