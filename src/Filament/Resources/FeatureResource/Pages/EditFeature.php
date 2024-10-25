<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Resources\FeatureResource\Pages;

use A21ns1g4ts\FilamentStripe\Actions\UpFromStripe;
use A21ns1g4ts\FilamentStripe\Actions\UpToStripe;
use A21ns1g4ts\FilamentStripe\Filament\Resources\FeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeature extends EditRecord
{
    protected static string $resource = FeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('updateFromStripe')
                ->label('Up From Stripe')
                ->action(function () {
                    UpFromStripe::run($this->record, 'feature');

                    return redirect($this->getUrl(['record' => $this->record->id])); // @phpstan-ignore-line
                }),
            Actions\Action::make('updateToStripe')
                ->label('Up To Stripe')
                ->action(function () {
                    UpToStripe::run($this->record, 'feature');

                    return redirect($this->getUrl(['record' => $this->record->id])); // @phpstan-ignore-line
                }),
        ];
    }
}
