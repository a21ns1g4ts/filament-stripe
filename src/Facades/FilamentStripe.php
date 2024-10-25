<?php

namespace A21ns1g4ts\FilamentStripe\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \A21ns1g4ts\FilamentStripe\FilamentStripe
 */
class FilamentStripe extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \A21ns1g4ts\FilamentStripe\FilamentStripe::class;
    }
}
