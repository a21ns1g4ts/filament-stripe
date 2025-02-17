<?php

namespace A21ns1g4ts\FilamentStripe\Commands;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\CreateWebhookEndpoints;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\UpdateWebhookEndpoint;
use Illuminate\Console\Command;

class WebhookCommand extends Command
{
    public $description = 'Configure Stripe webhook';

    protected $signature = 'filament-stripe:webhook
            {--disabled : Immediately disable the webhook after creation}
            {--url= : The URL endpoint for the webhook}
            {--api-version= : The Stripe API version the webhook should use}';

    public const DEFAULT_EVENTS = [
        'customer.subscription.created',
        'customer.subscription.updated',
        'customer.subscription.deleted',
        'customer.updated',
        // 'customer.deleted',
        // 'payment_method.automatically_updated',
        // 'invoice.payment_action_required',
        // 'invoice.payment_succeeded',
    ];

    public function handle()
    {
        $endpoint = CreateWebhookEndpoints::run(array_filter([
            'enabled_events' => config('cashier.webhook.events') ?: self::DEFAULT_EVENTS,
            'url' => $this->option('url') ?? route('cashier.webhook'),
            'api_version' => $this->option('api-version'),
        ]));

        $this->components->info('The Stripe webhook was created successfully. Retrieve the webhook secret in your Stripe dashboard and define it as an environment variable.');

        if ($this->option('disabled')) {
            UpdateWebhookEndpoint::run($endpoint->id, ['disabled' => true]);

            $this->components->info('The Stripe webhook was disabled as requested. You may enable the webhook via the Stripe dashboard when needed.');
        }
    }
}
