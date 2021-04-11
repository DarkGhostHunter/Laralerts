<?php

namespace DarkGhostHunter\Laralerts;

use DarkGhostHunter\Laralerts\Contracts\Renderer;
use DarkGhostHunter\Laralerts\Http\Middleware\AddAlertsToJson;
use DarkGhostHunter\Laralerts\Http\Middleware\StorePersistentAlertsInSession;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Session\Session;
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
        $this->mergeConfigFrom(__DIR__ . '/../config/laralerts.php', 'laralerts');

        $this->app->singleton(RendererManager::class);
        $this->app->singleton(
            Renderer::class,
            static function ($app) {
                return $app->make(RendererManager::class)->driver(
                    $app->make(Repository::class)->get('laralerts.renderer')
                );
            }
        );

        $this->app->singleton(Bag::class);

        $this->app->bind(
            StorePersistentAlertsInSession::class,
            static function ($app) {
                return new StorePersistentAlertsInSession(
                    $app->make(Bag::class),
                    $app->make(Session::class),
                    $app->make(Repository::class)->get('laralerts.key')
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @param  \Illuminate\Foundation\Http\Kernel  $http
     * @param  \Illuminate\Routing\Router  $router
     *
     * @return void
     */
    public function boot(Kernel $http, Router $router)
    {
        // Add the Global Middleware to the `web` group only if it exists.
        if (array_key_exists('web', $http->getMiddlewareGroups())) {
            $http->appendMiddlewareToGroup('web', StorePersistentAlertsInSession::class);
        }

        // Add it to the middleware priority as last.
        $http->appendToMiddlewarePriority(StorePersistentAlertsInSession::class);

        $router->aliasMiddleware('laralerts.json', AddAlertsToJson::class);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laralerts');

        $this->callAfterResolving(
            'blade.compiler',
            function ($blade) {
                $blade->component(View\Component\LaralertsComponent::class, 'laralerts');
            }
        );

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../resources/views' => resource_path('views/vendor/laralerts')], 'views');
            $this->publishes([__DIR__ . '/../config/laralerts.php' => config_path('laralerts.php')], 'config');
        }
    }
}
