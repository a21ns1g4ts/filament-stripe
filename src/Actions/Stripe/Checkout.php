<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use A21ns1g4ts\FilamentStripe\Actions\GetOrCreateCustomer;
use A21ns1g4ts\FilamentStripe\Models\Price;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\Concerns\AsAction;

// TODO: Test
class Checkout extends StripeBaseAction
{
    use AsAction;

    /**
     * Handle the checkout session creation.
     */
    public function handle($billable, Price $price, $successUrl = null, $cancelUrl = null, $mode = 'subscription', array $data = [])
    {
        $meteredPrices = $price->product->features()
            ->wherePivot('price_id', '!=', null)
            ->wherePivot('meteread', true)
            ->get()
            ->pluck('pivot.price.stripe_id')
            ->filter()
            ->toArray();

        $lineItems = [
            [
                'price' => $price->stripe_id,
                'quantity' => 1,
            ],
        ];

        foreach ($meteredPrices as $meteredPrice) {
            $lineItems[] = [
                'price' => $meteredPrice,
            ];
        }

        $customer = GetOrCreateCustomer::run($billable, $data);

        $data = array_merge($data, [
            'customer' => $customer->stripe_id,
            'line_items' => $lineItems,
            'mode' => $mode,
            'ui_mode' => 'hosted',
            'payment_method_data' => [
                'allow_redisplay' => 'always',
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        $checkoutSession = $this->stripe->checkout->sessions->create($data);

        return Redirect::to($checkoutSession->url, 303);
    }
}
