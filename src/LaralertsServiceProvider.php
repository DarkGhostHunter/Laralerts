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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laralerts.php', 'laralerts'
        );

        $this->app->singleton(AlertBag::class, function ($app) {
            return $app['session.store']->get($app['config']['laralerts.key']) ?? new AlertBag();
        });

        $this->app->singleton(AlertManager::class);
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

        // @codeCoverageIgnoreStart
        $this->app['blade.compiler']->directive(
            $this->app->make('config')->get('laralerts.directive'),
            function () {
                return "<?php echo \$__env->make('laralerts::alerts', [], ['alerts' => app(\DarkGhostHunter\Laralerts\AlertBag::class)->getAlerts()])->render(); ?>";
            }
        );
        // @codeCoverageIgnoreEnd
    }
}
