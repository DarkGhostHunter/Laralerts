<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Support\ServiceProvider;

class LaralertsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AlertBag::class);
        $this->app->singleton(AlertFactory::class);

        // Let's tell the Service Container to add our @alert directive only when
        // the developer is using the Blade Compiler engine. The directive will
        // resolve the Alert Bag from the Service Container and render it.
        $this->app->resolving('blade.compiler', static function($blade, $app) {
            /** @var \Illuminate\View\Compilers\BladeCompiler $blade */
            $blade->directive('alerts', static function() use ($app) {
                /** @var \Illuminate\Foundation\Application $app */
                return $app->make(AlertBag::class);
            });
        });
    }

}