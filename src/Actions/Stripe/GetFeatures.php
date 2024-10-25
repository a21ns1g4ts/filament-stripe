<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class GetFeatures extends StripeBaseAction
{
    use AsAction;

    public function handle($limit = 100)
    {
        return $this->stripe->entitlements->features->all(['limit' => $limit]);
    }
}
