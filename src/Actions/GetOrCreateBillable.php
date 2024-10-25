<?php

namespace A21ns1g4ts\FilamentStripe\Actions;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\CreateCustomer;
use Lorisleiva\Actions\Concerns\AsAction;

class GetOrCreateBillable
{
    use AsAction;

    public function handle($user, array $data = [])
    {
        $data['email'] = $data['email'] ?? $user->email;
        $data['name'] = $data['name'] ?? $user->name;

        if ($user->billable()->exists()) {
            return $user->billable;
        }

        $customer = CreateCustomer::run($data['name'], $data['email'], $data);
        $data['stripe_id'] = $customer->id;

        $billable = $user->billable()->create($data);
        $user->update(['billable_id' => $billable->id]);

        return $billable;
    }
}
