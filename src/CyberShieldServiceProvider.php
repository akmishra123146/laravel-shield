<?php

namespace CyberSec\Shield;

use Illuminate\Support\ServiceProvider;
use CyberSec\Shield\Console\Commands\ScanVulnerabilitiesCommand;
use CyberSec\Shield\Http\Middleware\ZeroTrustMiddleware;
use CyberSec\Shield\Http\Middleware\EndpointSecurityShield;

class CyberShieldServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/cybershield.php', 'cybershield'
        );

        $this->app->singleton('cybershield', function ($app) {
            return new CyberShieldManager($app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cybershield.php' => config_path('cybershield.php'),
            ], 'cybershield-config');

            $this->commands([
                ScanVulnerabilitiesCommand::class,
            ]);
        }

        $router = $this->app['router'];
        $router->aliasMiddleware('cybershield.zero_trust', ZeroTrustMiddleware::class);
        $router->aliasMiddleware('cybershield.waf', EndpointSecurityShield::class);
    }
}
