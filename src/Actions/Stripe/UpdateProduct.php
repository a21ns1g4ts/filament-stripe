<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProduct extends StripeBaseAction
{
    use AsAction;

    public function handle(string $stripeId, array $data)
    {
        $data = [
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'active' => $data['active'] ?? null,
            'default_price' => $data['default_price'] ?? null,
            'images' => $data['images'] ?? null,
            'marketing_features' => $data['marketing_features'] ?? null,
            'package_dimensions' => $data['package_dimensions'] ?? null,
            'shippable' => (bool) $data['shippable'] ?? null,
            'statement_descriptor' => $data['statement_descriptor'] ?? null,
            'tax_code' => $data['tax_code'] ?? null,
            'unit_label' => $data['unit_label'] ?? null,
            'url' => $data['url'] ?? null,
        ];

        // Stripe api will ignore keys not in $data
        $data = array_filter($data, fn($value) => ! is_null($value));

        return $this->stripe->products->update($stripeId, $data);
    }
}
