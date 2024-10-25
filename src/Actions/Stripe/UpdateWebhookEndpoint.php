<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

// TODO: Test
class UpdateWebhookEndpoint extends StripeBaseAction
{
    use AsAction;

    public function handle(string $stripeId, array $data)
    {
        return $this->stripe->webhookEndpoints->update($stripeId, $data);
    }
}
