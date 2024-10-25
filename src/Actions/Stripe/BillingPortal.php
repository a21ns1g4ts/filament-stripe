<?php

namespace A21ns1g4ts\FilamentStripe\Actions\Stripe;

use A21ns1g4ts\FilamentStripe\Filament\Pages\Plans;
use A21ns1g4ts\FilamentStripe\Models\Billable;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\Concerns\AsAction;

class BillingPortal extends StripeBaseAction
{
    use AsAction;

    public function handle(Billable $billable)
    {
        $session = $this->stripe->billingPortal->sessions->create([
            'customer' => $billable->stripe_id,
            'return_url' => Plans::getUrl(),
        ]);

        return Redirect::to($session->url, 303);
    }
}
