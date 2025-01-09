<?php

namespace A21ns1g4ts\FilamentStripe;

use A21ns1g4ts\FilamentStripe\Filament\Pages\Plans;
use A21ns1g4ts\FilamentStripe\Filament\Resources\CustomerResource;
use A21ns1g4ts\FilamentStripe\Filament\Resources\FeatureResource;
use A21ns1g4ts\FilamentStripe\Filament\Resources\PriceResource;
use A21ns1g4ts\FilamentStripe\Filament\Resources\ProductResource;
use Filament\Contracts\Plugin;
use Filament\FilamentManager;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Illuminate\Support\Facades\App;

class FilamentStripePlugin implements Plugin
{
    protected $settings;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'a21ns1g4ts.filament-stripe';
    }

    public static function get(): Plugin|FilamentManager
    {
        return filament(app(static::class)->getId());
    }

    public function boot(Panel $panel): void
    {
        if (App::runningInConsole()) {
            return;
        }
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            Plans::class,
        ])
            ->userMenuItems([
                'plans' => MenuItem::make()
                    ->label(fn() => __('filament-stripe::default.plans.title'))
                    ->hidden(fn() => ! auth()->user()?->can('page_Plans'))
                    ->url(fn() => $this->getPageUrl($panel))
                    ->icon('heroicon-o-credit-card'),
            ]);

        if ($panel->getId() === 'sysadmin') {
            $panel->resources([
                CustomerResource::class,
                ProductResource::class,
                PriceResource::class,
                FeatureResource::class,
            ]);
        }
    }

    private function getPageUrl(Panel $panel)
    {
        if ($panel->getId() === 'admin' && auth()->user()?->currentCompany?->id) {
            return Plans::getUrl(['tenant' => auth()->user()?->currentCompany?->id]);
        }

        if ($panel->getId() === 'admin' && ! auth()->user()?->currentCompany?->id) {
            return '#';
        }

        return Plans::getUrl();
    }
}
