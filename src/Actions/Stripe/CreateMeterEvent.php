<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

// TODO: Test
class CreateMeterEvent extends StripeBaseAction
{
    use AsAction;

    public function handle(string $name, string $customerId, int $value = 1)
    {
        $data['name'] = $name;
        $data['payload'] = [
            'value' => $value,
            'stripe_customer_id' => $customerId
        ];

        return $this->stripe->billing->meterEvents->create($data);
    }
}
