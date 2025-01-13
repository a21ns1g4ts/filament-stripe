<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSubscription extends StripeBaseAction
{
    use AsAction;

    public function handle(string $stripeId, array $data)
    {
        return $this->stripe->subscriptions->update($stripeId, $data);
    }
}
