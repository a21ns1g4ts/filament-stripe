<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Resources;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetFeatures;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetPrices;
use A21ns1g4ts\FilamentStripe\Filament\Resources\FeatureResource\Pages;
use A21ns1g4ts\FilamentStripe\Models\Feature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Novadaemon\FilamentPrettyJson\PrettyJson;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Stripe';

    public static function form(Form $form): Form
    {
        $features = Feature::pluck('name', 'stripe_id');

        return $form
            ->schema([
                Forms\Components\Section::make('Stripe Information')
                    ->schema([
                        Forms\Components\Select::make('stripe_id')
                            ->required()
                            ->options(fn (Get $get): array => self::getFeatures())
                            ->disableOptionWhen(fn (string $value): bool => $features->has($value))
                            ->searchable()
                            ->columnSpan(2),
                    ])->columns(3),

                Forms\Components\Section::make('Product Settings')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lookup_key')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('active')
                            ->disabled(),
                        PrettyJson::make('metadata')
                            ->disabled(),
                        Forms\Components\Toggle::make('livemode')
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }

    public static function getFeatures(): array
    {
        return collect(GetFeatures::run(100))
            ->map(fn ($price) => [
                'id' => $price->id,
                'text' => "{$price->name} - {$price->id}",
            ])
            ->pluck('text', 'id')
            ->toArray();
    }

    public static function getPrices(): array
    {
        return collect(GetPrices::run(100))
            ->map(fn ($price) => [
                'id' => $price->id,
                'text' => "{$price->nickname} - {$price->id}",
            ])
            ->pluck('text', 'id')
            ->toArray();
    }
}
