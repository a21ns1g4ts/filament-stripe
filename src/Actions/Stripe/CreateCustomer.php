<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class CreateCustomer extends StripeBaseAction
{
    use AsAction;

    public function handle(string $name, string $email, array $data)
    {
        $data['email'] = $email;
        $data['name'] = $name;

        return $this->stripe->customers->create($data);
    }
}
