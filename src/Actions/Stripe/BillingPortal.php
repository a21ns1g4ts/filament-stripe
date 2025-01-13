<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use A21ns1g4ts\FilamentStripe\Models\Customer;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\Concerns\AsAction;

class BillingPortal extends StripeBaseAction
{
    use AsAction;

    public function handle(Customer $customer, $returnUrl = null)
    {
        $session = $this->stripe->billingPortal->sessions->create([
            'customer' => $customer->stripe_id,
            'return_url' => $returnUrl,
        ]);

        return Redirect::to($session->url, 303);
    }
}
