<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use DarkGhostHunter\Laralerts\Http\Middleware\ExpireAlerts;
use DarkGhostHunter\Laralerts\Http\Middleware\AppendAlertsToJsonResponse;

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

        $this->app->singleton(AlertBag::class, function ($app) {
            return $app['session.store']->get($app['config']['laralerts.key']) ?? new AlertBag();
        });

        $this->app->singleton(AlertManager::class, function ($app) {
            $config = $app['config'];

            return new AlertManager(
                $app->make(AlertBag::class),
                $app['session.store'],
                $config->get('laralerts.key'),
                $config->get('laralerts.type'),
                $config->get('laralerts.dismiss')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laralerts');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laralerts'),
        ]);

        // Register the terminable middleware that ages the alerts automatically
        $this->app[Kernel::class]->pushMiddleware(ExpireAlerts::class);

        // Middleware aliasing
        $this->app[Router::class]->aliasMiddleware('alert.json', AppendAlertsToJsonResponse::class);

        // @codeCoverageIgnoreStart
        $this->app['blade.compiler']->directive( $this->app->make('config')->get('laralerts.directive'), function () {
            return "<?php echo \$__env->make('laralerts::alerts', [], ['alerts' => app(\DarkGhostHunter\Laralerts\AlertBag::class)->getAlerts()])->render(); ?>";
        });
        // @codeCoverageIgnoreEnd
    }
}
