<?php

namespace A21ns1g4ts\FilamentStripe;

use A21ns1g4ts\FilamentStripe\Commands\WebhookCommand;
use A21ns1g4ts\FilamentStripe\Currencies\BRL;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stripe\Stripe;

class FilamentStripeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-stripe')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoutes('web')
            ->hasMigrations(
                'create_customers_table',
                'create_products_table',
                'create_prices_table',
                'create_features_table',
                'create_feature_product_table',
                'create_subscriptions_table',
                'create_subscription_items_table'
            )
            ->hasCommand(WebhookCommand::class);
    }

    public function bootingPackage(): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        currencies()->add(new BRL);
    }
}
