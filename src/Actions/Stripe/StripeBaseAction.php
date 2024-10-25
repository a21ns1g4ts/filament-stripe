<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use Stripe\StripeClient;

abstract class StripeBaseAction
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    protected function prepareData(array $data): array
    {
        return array_filter($data, fn ($value) => ! is_null($value));
    }
}
