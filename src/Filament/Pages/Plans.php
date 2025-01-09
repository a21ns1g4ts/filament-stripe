<?php

namespace A21ns1g4ts\FilamentStripe\Filament\Pages;

use A21ns1g4ts\FilamentStripe\Actions\GetOrCreateCustomer;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\BillingPortal;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\CancelSubscription;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\Checkout;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\CreateSubscription;
use A21ns1g4ts\FilamentStripe\Models\Price;
use Filament\Actions as FilamentActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Stripe\Subscription as StripeSubscription;

class Plans extends Page
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string $view = 'filament-stripe::plans';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string|Htmlable
    {
        return __('filament-stripe::default.plans.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            FilamentActions\Action::make('billingPortal')
                ->label(__('filament-stripe::default.billing-portal.title'))
                ->icon('heroicon-o-user-circle')
                ->action(function () {
                    if (self::getCostumer()) {
                        BillingPortal::run(self::getCostumer());
                    } else {
                        $customer = GetOrCreateCustomer::run(self::getBillable());

                        BillingPortal::run($customer);
                    }
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $plans = Price::where('type', 'recurring')->where('recurring->meter', null)->get();

        return $infolist
            ->state(['plans' => $plans])
            ->schema([
                RepeatableEntry::make('plans')
                    ->label('')
                    ->schema([
                        TextEntry::make('product.name')
                            ->hiddenLabel()
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('primary'),
                        TextEntry::make('unit_amount')
                            ->hiddenLabel()
                            ->size(TextEntry\TextEntrySize::Medium)
                            ->weight(FontWeight::SemiBold)
                            ->currency(Str::upper($record->currency)),
                        RepeatableEntry::make('product.features')
                            ->label('Features')
                            ->schema([
                                TextEntry::make('name')
                                    ->formatStateUsing(fn ($record) => $record->valueing($record->pivot))
                                    ->hiddenLabel()
                                    ->columnSpan(1),
                                TextEntry::make('name')
                                    ->hiddenLabel()
                                    ->columnSpan(2),
                                TextEntry::make('name')
                                    ->formatStateUsing(fn ($record) => $record->pricing($record->pivot))
                                    ->hiddenLabel()
                                    ->columnSpan(3),
                            ])->columns(6),
                        Actions::make([
                            Action::make('subscribe')
                                ->icon('heroicon-m-check')
                                ->label(fn ($record) => self::getCostumer()?->subscribed($record->stripe_id) ? __('filament-stripe::default.plans.subscribed') : __('filament-stripe::default.plans.subscribe'))
                                ->hidden(fn ($record) => self::getCostumer()?->subscribed($record->stripe_id))
                                ->action(fn ($record, $action) => self::subscribe($record, $action)),
                            Action::make('cancel')
                                ->icon('heroicon-o-x-mark')
                                ->label(fn ($record) => __('filament-stripe::default.plans.cancel'))
                                ->color('danger')
                                ->requiresConfirmation()
                                ->hidden(fn ($record) => ! self::getCostumer()?->subscribed($record->stripe_id))
                                ->action(fn ($record, $action) => self::cancel($record, $action)),
                        ])
                            ->alignment(Alignment::Center)
                            ->fullWidth(),
                    ])
                    ->grid(3),
            ]);
    }

    public static function getBillable()
    {
        return Session::get('billable') ?? auth()->user();
    }

    private static function getCostumer()
    {
        return Session::get('customer');
    }

    private static function subscribe($record, $action)
    {
        $customer = self::getCostumer();
        $billable = self::getBillable();
        if (! $customer) {
            return Checkout::run($billable, $record);
        }

        $subscription = $customer->subscriptions()
            ->whereStatus(StripeSubscription::STATUS_ACTIVE)
            ->first();
        if ($subscription) {
            Notification::make()
                ->danger()
                ->title(__('filament-stripe::default.plans.notify.already_subscribed.title'))
                ->body(__('filament-stripe::default.plans.notify.already_subscribed.body'))
                ->send();

            $action->cancel();
        } else {
            return CreateSubscription::run($billable, $customer, $record);
        }
    }

    private static function cancel($record, $action)
    {
        $customer = self::getCostumer();

        if (! $customer) {
            Notification::make()
                ->danger()
                ->title(__('filament-stripe::default.plans.notify.not_subscribed.title'))
                ->body(__('filament-stripe::default.plans.notify.not_subscribed.body'))
                ->send();

            $action->cancel();

            return;
        }

        $subscription = $customer->subscriptions()
            ->where('stripe_id', $record->stripe_price)
            ->whereStatus(StripeSubscription::STATUS_ACTIVE)
            ->first();

        if (! $subscription) {
            $subscription = $customer->subscriptions()
                ->whereStatus(StripeSubscription::STATUS_ACTIVE)
                ->get()
                ->map(fn ($record) => $record->items)
                ->firstWhere('stripe_price', $record->stripe_price)
                ->first()
                ?->subscription;
        }

        if ($subscription) {
            $stripeSubscription = CancelSubscription::run($subscription);
            if ($stripeSubscription) {
                Notification::make()
                    ->success()
                    ->title(__('filament-stripe::default.plans.notify.subscription_canceled.title'))
                    ->body(__('filament-stripe::default.plans.notify.subscription_canceled.body'))
                    ->send();
            }
        } else {
            Notification::make()
                ->danger()
                ->title(__('filament-stripe::default.plans.notify.subscription_cant_canceled.title'))
                ->body(__('filament-stripe::default.plans.notify.subscription_cant_canceled.body'))
                ->persistent()
                ->send();

            $action->cancel();
        }

        sleep(2);

        return redirect(Plans::getUrl());
    }
}
