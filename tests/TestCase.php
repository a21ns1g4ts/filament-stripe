<?php

namespace A21ns1g4ts\FilamentStripe\Tests;

use A21ns1g4ts\FilamentStripe\FilamentStripeServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Stripe\Stripe;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'A21ns1g4ts\\FilamentStripe\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        Stripe::setApiKey(config('services.stripe.secret'));
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentStripeServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');

        $config->set('database.default', 'sqlite');
        $config->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $config->set('services.stripe.secret', 'sk_test_12345');
    }
}
