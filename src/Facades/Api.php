<?php

namespace Sepehrgostar\Api\Facades;

use Illuminate\Support\Facades\Facade;

class Api extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'api';
    }
}
