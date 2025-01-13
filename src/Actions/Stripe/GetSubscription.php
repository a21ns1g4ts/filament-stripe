<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class GetSubscription extends StripeBaseAction
{
    use AsAction;

    public function handle($stripeId)
    {
        return $this->stripe->subscriptions->retrieve($stripeId);
    }
}
