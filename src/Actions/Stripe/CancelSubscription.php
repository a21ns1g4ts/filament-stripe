<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use A21ns1g4ts\FilamentStripe\Models\Subscription;
use Lorisleiva\Actions\Concerns\AsAction;

// TODO: Test
class CancelSubscription extends StripeBaseAction
{
    use AsAction;

    public function handle(Subscription $subscription, $prorate = false)
    {
        return $this->stripe->subscriptions->cancel($subscription->stripe_id, ['prorate' => $prorate]);
    }
}
