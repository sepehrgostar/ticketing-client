<?php

namespace Sepehrgostar\LaravelClient\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'LaravelClient';
    }
}
