<?php

namespace A21ns1g4ts\FilamentStripe;

use A21ns1g4ts\FilamentStripe\Commands\FilamentStripeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasMigration('create_filament_stripe_table')
            ->hasCommand(FilamentStripeCommand::class);
    }
}
