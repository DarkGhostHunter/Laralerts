<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Routing\Router;
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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laralerts.php', 'laralerts'
        );

        $this->app->singleton(AlertBag::class);
        $this->app->singleton(AlertFactory::class, static function ($app) {
            return AlertBuilder::build($app);
        });

        $this->registerComponent();
    }

    /**
     * Register the Blade Component
     *
     * @return void
     */
    protected function registerComponent()
    {
        $this->app->resolving('blade.compiler', static function ($blade, $app) {
            /** @var \Illuminate\View\Compilers\BladeCompiler $blade */
            $blade->directive($app->make('config')->get('laralerts.directive'), function () use ($app) {
                return $app->make(AlertsHtml::class);
            });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laralerts');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laralerts'),
        ]);
    }
}