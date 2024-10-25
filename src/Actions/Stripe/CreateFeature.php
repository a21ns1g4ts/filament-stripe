<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class CreateFeature extends StripeBaseAction
{
    use AsAction;

    public function handle(string $name)
    {
        $data['name'] = $name;

        return $this->stripe->entitlements->features->create($data);
    }
}
