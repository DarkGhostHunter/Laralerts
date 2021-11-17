<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class LaralertsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laralerts.php', 'laralerts');

        $this->app->singleton(RendererManager::class);
        $this->app->singleton(Contracts\Renderer::class, static function (Container $app): Contracts\Renderer {
            return $app->make(RendererManager::class)->driver($app->make('config')->get('laralerts.renderer'));
        });

        $this->app->singleton(Bag::class, static function (Container $app): Bag {
            return new Bag((array) $app->make('config')->get('laralerts.tags', ['default']));
        });

        $this->app->bind(
            Http\Middleware\StoreAlertsInSession::class,
            static function (Container $app): Http\Middleware\StoreAlertsInSession {
                return new Http\Middleware\StoreAlertsInSession(
                    $app->make(Bag::class),
                    $app->make('session.store'),
                    $app->make('config')->get('laralerts.key')
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
    public function boot(Kernel $http, Router $router): void
    {
        // Add the Global Middleware to the `web` group only if it exists.
        if (array_key_exists('web', $http->getMiddlewareGroups())) {
            $http->appendMiddlewareToGroup('web', Http\Middleware\StoreAlertsInSession::class);
        }

        $router->aliasMiddleware('laralerts.json', Http\Middleware\AddAlertsToJson::class);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laralerts');

        $this->callAfterResolving('blade.compiler', static function (BladeCompiler $blade): void {
            $blade->component(View\Component\LaralertsComponent::class, 'laralerts');
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../resources/views' => resource_path('views/vendor/laralerts')], 'views');
            $this->publishes([__DIR__ . '/../config/laralerts.php' => config_path('laralerts.php')], 'config');
        }
    }
}
