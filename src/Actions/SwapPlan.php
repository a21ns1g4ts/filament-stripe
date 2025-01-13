<?php

namespace A21ns1g4ts\FilamentStripe\Actions;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\GetSubscription;
use A21ns1g4ts\FilamentStripe\Actions\Stripe\UpdateSubscription;
use A21ns1g4ts\FilamentStripe\Models\Subscription;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;

class SwapPlan
{
    use AsAction;

    protected Subscription $subscription;

    protected $prices;

    /**
     * Handle the checkout session creation.
     */
    public function handle(Subscription $subscription, $prices)
    {
        $this->subscription = $subscription;
        $this->prices = $prices;

        if (empty($prices = (array) $prices)) {
            throw new InvalidArgumentException('Please provide at least one price when swapping.');
        }

        $items = $this->mergeItemsThatShouldBeDeletedDuringSwap(
            $this->parseSwapPrices($prices)
        );

        $stripeSubscription = UpdateSubscription::run(
            $this->subscription->stripe_id, [
                'items' => $items->values()->all(),
                'payment_behavior' => 'allow_incomplete',
                'promotion_code' => null,
                'proration_behavior' => 'create_prorations',
                'expand' => ['latest_invoice.payment_intent'],
            ]
        );

        /** @var \Stripe\SubscriptionItem $firstItem */
        $firstItem = $stripeSubscription->items->first();
        $isSinglePrice = $stripeSubscription->items->count() === 1;

        $this->subscription->fill([
            'stripe_status' => $stripeSubscription->status,
            'stripe_price' => $isSinglePrice ? $firstItem->price->id : null,
            'quantity' => $isSinglePrice ? ($firstItem->quantity ?? null) : null,
            'ends_at' => null,
        ])->save();

        $subscriptionItemIds = [];

        foreach ($stripeSubscription->items as $item) {
            $subscriptionItemIds[] = $item->id;

            $this->subscription->items()->updateOrCreate([
                'stripe_id' => $item->id,
            ], [
                'stripe_product' => $item->price->product,
                'stripe_price' => $item->price->id,
                'quantity' => $item->quantity ?? null,
            ]);
        }

        // Delete items that aren't attached to the subscription anymore...
        $this->subscription->items()->whereNotIn('stripe_id', $subscriptionItemIds)->delete();

        $this->subscription->unsetRelation('items');
    }

    /**
     * Merge the items that should be deleted during swap into the given items collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function mergeItemsThatShouldBeDeletedDuringSwap(Collection $items)
    {
        $stripeSubscription = GetSubscription::run($this->subscription->stripe_id);

        /** @var \Stripe\SubscriptionItem $stripeSubscriptionItem */
        foreach ($stripeSubscription->items->data as $stripeSubscriptionItem) {
            $price = $stripeSubscriptionItem->price;

            if (! $item = $items->get($price->id, [])) {
                $item['deleted'] = true;

                if ($price->recurring->usage_type == 'metered') {
                    $item['clear_usage'] = true;
                }
            }

            $items->put($price->id, $item + ['id' => $stripeSubscriptionItem->id]);
        }

        return $items;
    }

    /**
     * Parse the given prices for a swap operation.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function parseSwapPrices(array $prices)
    {
        $isSinglePriceSwap = $this->subscription->items()->count() === 1;

        return Collection::make($prices)->mapWithKeys(function ($options, $price) use ($isSinglePriceSwap) {
            $price = is_string($options) ? $options : $price;

            $options = is_string($options) ? [] : $options;

            if (! isset($options['price_data'])) {
                $payload['price'] = $price;
            }

            if ($isSinglePriceSwap && ! is_null($this->subscription->quantity)) {
                $payload['quantity'] = $this->subscription->quantity;
            }

            return [$price => array_merge($payload, $options)];
        });
    }
}
