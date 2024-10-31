<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Resources\CustomerResource\Pages;

use A21ns1g4ts\FilamentStripe\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
