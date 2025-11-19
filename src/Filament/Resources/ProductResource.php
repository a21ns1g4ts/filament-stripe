<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Resources;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetProducts;
use A21ns1g4ts\FilamentStripe\Filament\Resources\ProductResource\Pages;
use A21ns1g4ts\FilamentStripe\Filament\Resources\ProductResource\RelationManagers\PricesRelationManager;
use A21ns1g4ts\FilamentStripe\Models\Feature;
use A21ns1g4ts\FilamentStripe\Models\Price;
use A21ns1g4ts\FilamentStripe\Models\Product;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|UnitEnum|null $navigationGroup = 'Stripe';

    protected static ?string $slug = 'stripe/products';

    public static function isScopedToTenant(): bool
    {
        return config('filament-stripe.tenant_scope', false);
    }

    public static function form(Schema $schema): Schema
    {
        $products = Product::pluck('name', 'stripe_id');

        return $schema->components([
            Section::make('Stripe Information')
                ->schema([
                    Forms\Components\Select::make('stripe_id')
                        ->label('Stripe Product')
                        ->required()
                        ->options(fn (Get $get): array => self::getProducts())
                        ->disableOptionWhen(fn (string $value): bool => $products->has($value))
                        ->searchable()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('stripe_id')
                        ->label('Stripe ID')
                        ->maxLength(255)
                        ->readOnly(),
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options(collect(['plan', 'feature', 'service', 'sku'])->mapWithKeys(fn ($type) => [$type => ucfirst($type)])),
                    Forms\Components\TextInput::make('name')
                        ->label('Product Name')
                        ->prefixIcon('bi-stripe')
                        ->maxLength(255)
                        ->nullable(),
                ])->columns(3),

            Section::make('Product Attributes')
                ->schema([
                    Forms\Components\TextInput::make('description')
                        ->label('Description')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Group::make([
                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->disabled(),
                        Forms\Components\Toggle::make('livemode')
                            ->label('Live Mode')
                            ->disabled(),
                        Forms\Components\Toggle::make('shippable')
                            ->label('Shippable')
                            ->disabled(),
                    ]),
                ])->columns(3),

            Section::make('Additional Information')
                ->schema([
                    CodeEditor::make('metadata')
                        ->label('Metadata')
                        ->disabled(),
                    CodeEditor::make('default_price_data')
                        ->label('Default Price Data')
                        ->disabled(),
                    CodeEditor::make('images')
                        ->label('Images')
                        ->disabled(),
                    CodeEditor::make('marketing_features')
                        ->label('Marketing Features')
                        ->disabled(),
                    CodeEditor::make('package_dimensions')
                        ->label('Package Dimensions')
                        ->disabled(),
                ])->columns(3),

            Section::make('Features')
                ->schema([
                    Repeater::make('features')
                        ->label('Features')
                        ->relationship('featureProducts')
                        ->schema([
                            Group::make([
                                Forms\Components\Select::make('feature_id')
                                    ->label('Feature')
                                    ->options(Feature::query()->pluck('name', 'id'))
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->searchable()
                                    ->columnSpan(2),
                                Forms\Components\Select::make('price_id')
                                    ->label('Price')
                                    ->options(Price::all()->pluck('product.name', 'id'))
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $price = Price::find($state);

                                        $set('unit_amount', $price?->unit_amount);
                                    })
                                    ->searchable()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('unit_amount')
                                    ->label('Unit Amount')
                                    ->numeric()
                                    ->nullable()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('value')
                                    ->label('Value')
                                    ->numeric()
                                    ->nullable()
                                    ->columnSpan(1),
                            ])->columns(6),
                            Group::make([
                                Forms\Components\Toggle::make('resetable')
                                    ->inline(false),
                                Forms\Components\Toggle::make('unlimited')
                                    ->inline(false),
                                Forms\Components\Toggle::make('meteread')
                                    ->inline(false),
                            ])->columns(10),
                        ])
                        ->extraItemActions([
                            Action::make('openService')
                                ->tooltip('Abrir serviço')
                                ->icon('heroicon-m-arrow-top-right-on-square')
                                ->url(function (array $arguments, Repeater $component): ?string {
                                    $itemData = $component->getRawItemState($arguments['item']);
                                    if (! $itemData['feature_id']) {
                                        return null;
                                    }

                                    $feature = Feature::find($itemData['feature_id']);
                                    if (! $feature) {
                                        return null;
                                    }

                                    return FeatureResource::getUrl('edit', ['record' => $feature]);
                                }, shouldOpenInNewTab: true)
                                ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['feature_id'])),
                        ])
                        ->orderColumn('sort')
                        ->defaultItems(0)
                        ->hiddenLabel()
                        ->columnSpanFull(),
                ])->columns(3),

            Section::make('Tax and URL Information')
                ->schema([
                    Forms\Components\TextInput::make('tax_code')
                        ->label('Tax Code')
                        ->maxLength(255)
                        ->readOnly(),
                    Forms\Components\TextInput::make('unit_label')
                        ->label('Unit Label')
                        ->maxLength(255)
                        ->readOnly(),
                    Forms\Components\TextInput::make('url')
                        ->label('Product URL')
                        ->maxLength(255)
                        ->readOnly(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('livemode')
                    ->boolean(),
                Tables\Columns\IconColumn::make('shippable')
                    ->boolean(),
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getProducts(): array
    {
        return collect(GetProducts::run(100))
            ->map(fn ($product) => [
                'id' => $product->id,
                'text' => "{$product->name} - {$product->id}",
            ])
            ->pluck('text', 'id')
            ->toArray();
    }
}
