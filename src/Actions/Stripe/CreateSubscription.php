<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use A21ns1g4ts\FilamentStripe\Models\Customer;
use A21ns1g4ts\FilamentStripe\Models\Price;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSubscription extends StripeBaseAction
{
    use AsAction;

    public function handle($billable, Customer $customer, Price $price, $successUrl = null, $cancelUrl = null, $mode = 'subscription', array $data = [])
    {
        $stripeCustomer = $this->stripe->customers->retrieve($customer->stripe_id);
        if (! $stripeCustomer?->default_source && ! $stripeCustomer?->invoice_settings?->default_payment_method && $price->unit_amount !== 0) { // @phpstan-ignore-line
            return Checkout::run($billable, $price, $successUrl, $cancelUrl, $mode, $data);
        }

        $meteredPrices = $price->product->features()
            ->wherePivot('price_id', '!=', null)
            ->wherePivot('meteread', true)
            ->get()
            ->pluck('pivot.price.stripe_id')
            ->filter()
            ->toArray();

        $items = [
            [
                'price' => $price->stripe_id,
                'quantity' => 1,
            ],
        ];

        foreach ($meteredPrices as $meteredPrice) {
            $items[] = [
                'price' => $meteredPrice,
            ];
        }

        $subscription = $this->stripe->subscriptions->create([
            'customer' => $customer->stripe_id,
            'items' => $items,
        ]);

        sleep(2);

        return $subscription;
    }
}
