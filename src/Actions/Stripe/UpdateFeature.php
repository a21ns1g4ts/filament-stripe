<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateFeature extends StripeBaseAction
{
    use AsAction;

    public function handle(string $stripeId, array $data)
    {
        $data = [
            'name' => $data['name'] ?? null,
            'lookup_key' => $data['lookup_key'] ?? null,
            // 'metadata' => $data['metadata'],
            // 'active' => $data['active'],
        ];

        // Stripe api will ignore keys not in $data
        $data = array_filter($data, fn ($value) => ! is_null($value));

        return $this->stripe->entitlements->features->update($stripeId, $data);
    }
}
