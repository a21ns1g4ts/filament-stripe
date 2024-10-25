<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Resources\ProductResource\Pages;

use A21ns1g4ts\FilamentStripe\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'plans' => Tab::make()->query(fn ($query) => $query->where('type', 'plan')),
            'features' => Tab::make()->query(fn ($query) => $query->where('type', 'feature')),
            'skus' => Tab::make()->query(fn ($query) => $query->where('type', 'sku')),
            'services' => Tab::make()->query(fn ($query) => $query->where('type', 'service')),
        ];
    }
}
