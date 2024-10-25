<?php

namespace A21ns1g4ts\FilamentStripe\Commands;

use Illuminate\Console\Command;

class FilamentStripeCommand extends Command
{
    public $signature = 'filament-stripe';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
