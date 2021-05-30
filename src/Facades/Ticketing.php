<?php

namespace Sepehrgostar\Ticketing\Facades;

use Illuminate\Support\Facades\Facade;

class Ticketing extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Ticketing';
    }
}
