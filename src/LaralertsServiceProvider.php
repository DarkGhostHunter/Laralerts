<?php

namespace DarkGhostHunter\Laralerts;

use DarkGhostHunter\Laralerts\Contracts\Renderer;
use DarkGhostHunter\Laralerts\Http\Middleware\LaralertsJsonMiddleware;
use DarkGhostHunter\Laralerts\Http\Middleware\LaralertsMiddleware;
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
        $this->app->singleton(Renderer::class, static function ($app) {
            return $app->make(RendererManager::class)->driver($app->make(Repository::class)->get('laralerts.renderer'));
        });

        $this->app->singleton(Bag::class);

        $this->app->bind(LaralertsMiddleware::class, static function ($app) {
            new LaralertsMiddleware(
                $app->make(Bag::class),
                $app->make(Session::class),
                $app->make(Repository::class)->get('laralerts.key')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Kernel $http, Router $router)
    {
        $http->pushMiddleware(LaralertsMiddleware::class);
        $router->aliasMiddleware('laralerts.json', LaralertsJsonMiddleware::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laralerts');

        $this->callAfterResolving('blade.compiler', function ($blade) {
            $blade->component(View\Component\Laralerts::class, 'laralerts');
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../resources/views' => resource_path('views/vendor/laralerts')], 'views');
        }
    }
}
