<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use A21ns1g4ts\FilamentStripe\Filament\Pages\Plans;
use A21ns1g4ts\FilamentStripe\Models\Billable;
use A21ns1g4ts\FilamentStripe\Models\Price;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSubscription extends StripeBaseAction
{
    use AsAction;

    public function handle(Model $user, Billable $billable, Price $price, string $mode = 'subscription', array $data = [])
    {
        $stripeCustomer = $this->stripe->customers->retrieve($billable->stripe_id);
        if (! $stripeCustomer?->default_source && ! $stripeCustomer?->invoice_settings?->default_payment_method) { // @phpstan-ignore-line
            return Checkout::run($user, $price, $mode, $data);
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

        // TODO: Check if there’s an way to show in the customer portal if there’s a metered price
        // If there’s a metered price, the list of plans and the option to switch plans will not appear in the customer portal.
        foreach ($meteredPrices as $meteredPrice) {
            $items[] = [
                'price' => $meteredPrice,
            ];
        }

        $this->stripe->subscriptions->create([
            'customer' => $billable->stripe_id,
            'items' => $items,
        ]);

        sleep(2);

        return redirect(Plans::getUrl());
    }
}
