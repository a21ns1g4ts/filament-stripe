<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

// TODO: Test
class CreateWebhookEndpoints extends StripeBaseAction
{
    use AsAction;

    public function handle(array $data = [])
    {
        $this->stripe->webhookEndpoints->create($data);
    }
}
