<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class CreateProduct extends StripeBaseAction
{
    use AsAction;

    public function handle(string $name, array $data)
    {
        $data['name'] = $name;

        return $this->stripe->products->create($data);
    }
}
