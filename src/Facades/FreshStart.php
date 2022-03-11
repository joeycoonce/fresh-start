<?php

namespace JoeyCoonce\FreshStart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JoeyCoonce\FreshStart\FreshStart
 */
class FreshStart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fresh-start';
    }
}
