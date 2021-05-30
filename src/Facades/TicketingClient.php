<?php

namespace Sepehrgostar\TicketingClient\Facades;

use Illuminate\Support\Facades\Facade;

class TicketingClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'TicketingClient';
    }
}
