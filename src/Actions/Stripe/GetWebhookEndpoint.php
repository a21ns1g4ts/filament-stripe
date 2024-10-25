<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

// TODO: Add test
class GetWebhookEndpoint extends StripeBaseAction
{
    use AsAction;

    public function handle(int $limit = 100)
    {
        return $this->stripe->webhookEndpoints->all(['limit' => $limit]);
    }
}
