<?php

namespace tanthammar\Recurring\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \tanthammar\Recurring\Recurring
 */
class Recurring extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'recurring';
    }
}
