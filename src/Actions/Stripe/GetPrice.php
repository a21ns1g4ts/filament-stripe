<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class GetPrice extends StripeBaseAction
{
    use AsAction;

    public function handle(string $stripeId)
    {
        return $this->stripe->prices->retrieve($stripeId, []);
    }
}
