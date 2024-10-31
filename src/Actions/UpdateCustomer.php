<?php

namespace A21ns1g4ts\FilamentStripe\Actions;

use A21ns1g4ts\FilamentStripe\Models\Customer;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCustomer
{
    use AsAction;

    public function handle(Customer $customer, array $data)
    {
        $customer->update($data);

        return $customer;
    }
}
