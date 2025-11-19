<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Resources;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetCustomers;
use A21ns1g4ts\FilamentStripe\Filament\Resources\CustomerResource\Pages;
use A21ns1g4ts\FilamentStripe\Models\Customer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\CodeEditor;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | UnitEnum | null $navigationGroup = 'Stripe';

    protected static ?string $slug = 'stripe/customers';

    public static function isScopedToTenant(): bool
    {
        return config('filament-stripe.tenant_scope', false);
    }

    public static function form(Schema $schema): Schema
    {
        $customers = Customer::pluck('name', 'stripe_id');

        return $schema->components([
            Section::make('Stripe Information')
                ->schema([
                    Forms\Components\Select::make('stripe_id')
                        ->required()
                        ->options(fn (Get $get): array => self::getCustomers())
                        ->disableOptionWhen(fn (string $value): bool => $customers->has($value))
                        ->searchable()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('stripe_id')
                        ->readonly()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('name')
                        ->maxLength(255),
                ])->columns(3),

            // Seção para detalhes do cliente
            Section::make('Client Details')
                ->schema([
                    Forms\Components\TextInput::make('description')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                ])->columns(3),

            Section::make('Balance and Settings')
                ->schema([
                    Forms\Components\TextInput::make('balance')
                        ->readonly()
                        ->numeric(),
                    Forms\Components\Toggle::make('delinquent')->disabled(),
                    Forms\Components\TextInput::make('invoice_prefix')
                        ->maxLength(255)
                        ->readonly(),
                ])->columns(3),

            Section::make('Additional Information')
                ->schema([
                    CodeEditor::make('address')->disabled(),
                    CodeEditor::make('metadata')->disabled(),
                    CodeEditor::make('shipping')->disabled(),
                    CodeEditor::make('cash_balance')->disabled(),
                    CodeEditor::make('discount')->disabled(),
                    CodeEditor::make('invoice_credit_balance')->disabled(),
                    CodeEditor::make('invoice_settings')->disabled(),
                    CodeEditor::make('preferred_locales')->disabled(),
                    CodeEditor::make('sources')->disabled(),
                    Forms\Components\Toggle::make('livemode')->disabled(),
                    Forms\Components\TextInput::make('next_invoice_sequence')
                        ->readonly()
                        ->numeric(),
                    Forms\Components\TextInput::make('tax_exempt')
                        ->readonly(),
                    CodeEditor::make('tax')->disabled(),
                    CodeEditor::make('tax_ids')->disabled(),
                    Forms\Components\TextInput::make('test_clock')
                        ->readonly()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('default_source')
                        ->readonly()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('coupon')
                        ->readonly()
                        ->maxLength(255),
                    Forms\Components\DateTimePicker::make('created')
                        ->readonly(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stripe_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('livemode')
                    ->boolean(),
                Tables\Columns\TextColumn::make('next_invoice_sequence')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created')
                    ->dateTime()
                    ->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getCustomers(): array
    {
        return collect(GetCustomers::run())
            ->map(fn ($customer) => [
                'id' => $customer->id,
                'text' => "{$customer->name} ({$customer->email}) - {$customer->id}",
            ])
            ->pluck('text', 'id')
            ->toArray();
    }
}
