<?php

namespace DarkGhostHunter\Laralerts;

use DarkGhostHunter\Laralerts\Http\Middleware\FlashAlertBagMiddleware;
use Illuminate\Contracts\Session\Session;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaralertsServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        AlertBag::class,
        AlertFactory::class,
    ];

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

        $this->registerContextualBindings();
        $this->registerComponent();
    }

    /**
     * Register the Contextual Binding for the classes
     *
     * @return void
     */
    protected function registerContextualBindings()
    {
        $this->app->when(AlertFactory::class)
            ->needs('$defaultType')
            ->give(static function ($app) {
                $app->make('config')->get('laralerts.type');
            });

        $this->app->when(FlashAlertBagMiddleware::class)
            ->needs('$sessionKey')
            ->give(static function ($app) {
                return $app->make('config')->get('laralerts.session_key');
            });
    }

    /**
     * Register the Blade Component
     *
     * @return void
     */
    protected function registerComponent()
    {
        $this->app->resolving('blade.compiler', static function ($blade, $app) {

            [$component, $session_key] = $app->make('config')->get('laralerts');

            $blade->component($component, static function () use ($app, $session_key) {
                return $app->make(Session::class)->get($session_key);
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
        $router->pushMiddlewareToGroup('web', FlashAlertBagMiddleware::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laralerts');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laralerts'),
        ]);
    }

}