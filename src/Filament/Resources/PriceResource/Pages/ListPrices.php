<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Resources\PriceResource\Pages;

use A21ns1g4ts\FilamentStripe\Filament\Resources\PriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrices extends ListRecords
{
    protected static string $resource = PriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
