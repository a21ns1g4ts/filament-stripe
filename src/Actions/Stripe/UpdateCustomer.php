<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCustomer extends StripeBaseAction
{
    use AsAction;

    public function handle(string $stripeId, array $data)
    {
        $data = [
            'address' => $data['address'] ?? null,
            'description' => $data['description'] ?? null,
            'email' => $data['email'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'name' => $data['name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'shipping' => $data['shipping'] ?? null,
            'balance' => $data['balance'] ??  null,
            'cash_balance' => $data['cash_balance'] ?? null,
            'coupon' => $data['coupon'] ?? null,
            'default_source' => $data['default_source'] ?? null,
            'invoice_prefix' => $data['invoice_prefix'] ?? null,
            'invoice_settings' => $data['invoice_settings'] ?? null,
            'next_invoice_sequence' => $data['next_invoice_sequence'] ?? null,
            'preferred_locales' => $data['preferred_locales'] ?? null,
            'promotion_code' => $data['promotion_code'] ?? null,
            'source' => $data['source'] ?? null,
            'tax' => $data['tax'] ?? null,
            'tax_exempt' => $data['tax_exempt'] ?? null,
        ];

        // Stripe api will ignore keys not in $data
        $data = array_filter($data, fn($value) => ! is_null($value));

        return $this->stripe->customers->update($stripeId, $data);
    }
}
