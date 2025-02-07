<?php

namespace A21ns1g4ts\FilamentStripe;

use A21ns1g4ts\FilamentStripe\Commands\WebhookCommand;
use A21ns1g4ts\FilamentStripe\Testing\TestsFilamentStripe;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stripe\Stripe;

class FilamentStripeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-stripe';

    public static string $viewNamespace = 'filament-stripe';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('a21ns1g4ts/filament-stripe');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        // FilamentAsset::register(
        //     $this->getAssets(),
        //     $this->getAssetPackageName()
        // );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-stripe/{$file->getFilename()}"),
                ], 'filament-stripe-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentStripe);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'a21ns1g4ts/filament-stripe';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-stripe', __DIR__ . '/../resources/dist/components/filament-stripe.js'),
            // Css::make('filament-stripe-styles', __DIR__.'/../resources/dist/filament-stripe.css'),
            // Js::make('filament-stripe-scripts', __DIR__.'/../resources/dist/filament-stripe.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            WebhookCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament_stripe_customers_table',
            'create_filament_stripe_products_table',
            'create_filament_stripe_prices_table',
            'create_filament_stripe_features_table',
            'create_filament_stripe_feature_product_table',
            'create_filament_stripe_subscriptions_table',
            'create_filament_stripe_subscription_items_table',
        ];
    }

    public function bootingPackage(): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
}
