<?php

namespace Joeycoonce\FreshStart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Joeycoonce\FreshStart\FreshStart
 */
class FreshStart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fresh-start';
    }
}
