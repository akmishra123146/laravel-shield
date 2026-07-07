<?php

namespace CyberSec\Shield;

use Illuminate\Contracts\Foundation\Application;

class CyberShieldManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new CyberShield manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the version of the CyberShield package.
     *
     * @return string
     */
    public function version()
    {
        return '1.0.0';
    }
}
