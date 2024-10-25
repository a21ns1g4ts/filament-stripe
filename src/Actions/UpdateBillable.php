<?php

namespace A21ns1g4ts\FilamentStripe\Actions;

use A21ns1g4ts\FilamentStripe\Models\Billable;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateBillable
{
    use AsAction;

    public function handle(Billable $billable, array $data)
    {
        $billable->update($data);

        return $billable;
    }
}
