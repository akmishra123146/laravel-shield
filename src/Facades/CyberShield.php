<?php

namespace CyberSec\Shield\Facades;

use Illuminate\Support\Facades\Facade;

class CyberShield extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cybershield';
    }
}
