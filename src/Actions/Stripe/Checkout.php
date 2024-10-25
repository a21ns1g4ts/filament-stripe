<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use A21ns1g4ts\FilamentStripe\Actions\GetOrCreateBillable;
use A21ns1g4ts\FilamentStripe\Filament\Pages\Plans;
use A21ns1g4ts\FilamentStripe\Models\Price;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\Concerns\AsAction;

// TODO: Test
class Checkout extends StripeBaseAction
{
    use AsAction;

    /**
     * Handle the checkout session creation.
     */
    public function handle(Model $user, Price $price, string $mode = 'subscription', array $data = [])
    {
        $meteredPrices = $price->product->features()
            ->whereNotNull('stripe_price')
            ->wherePivot('meteread', true)
            ->pluck('stripe_price')
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

        $billable = GetOrCreateBillable::run($user, $data);

        $data = array_merge($data, [
            'customer' => $billable->stripe_id,
            'line_items' => $lineItems,
            'mode' => $mode,
            'ui_mode' => 'hosted',
            'success_url' => Plans::getUrl(),
            'cancel_url' => Plans::getUrl(),
        ]);

        $checkoutSession = $this->stripe->checkout->sessions->create($data);

        return Redirect::to($checkoutSession->url, 303);
    }
}
