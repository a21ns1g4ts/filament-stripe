<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePrice extends StripeBaseAction
{
    use AsAction;

    public function handle(string $stripeId, array $data)
    {
        $data = [
            'currency_options' => $data['currency_options'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'active' => $data['active'] ?? null,
            'nickname' => $data['nickname'] ?? null,
            'tax_behavior' => $data['tax_behavior'] ?? null,
            'unit_label' => $data['unit_label'] ?? null,
            'lookup_key' => $data['lookup_key'] ?? null,
            'transfer_lookup_key' => $data['transfer_lookup_key'] ?? null,
        ];

        // Stripe api will ignore keys not in $data
        $data = array_filter($data, fn($value) => ! is_null($value));

        return $this->stripe->prices->update($stripeId, $data);
    }
}
