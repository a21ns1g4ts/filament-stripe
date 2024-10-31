<?php

namespace A21ns1g4ts\FilamentStripe\Actions;

use A21ns1g4ts\FilamentStripe\Actions\Stripe\CreateCustomer;
use A21ns1g4ts\FilamentStripe\Models\Customer;
use Lorisleiva\Actions\Concerns\AsAction;

class GetOrCreateCustomer
{
    use AsAction;

    public function handle($billable, array $data = [])
    {
        $data['email'] = $data['email'] ?? $billable->email;
        $data['name'] = $data['name'] ?? $billable->name;

        if ($customer = Customer::where('stripe_id', $billable->stripe_id)->exists()) {
            return $customer;
        }

        $customer = CreateCustomer::run($data['name'], $data['email'], $data);
        $data['stripe_id'] = $customer->id;
        $data['billable_id'] = $billable->id;
        $data['billable_type'] = get_class($billable);

        $customer = Customer::updateOrCreate(['stripe_id' => $data['stripe_id']], $data);

        return $customer;
    }
}
