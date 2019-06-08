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

        $this->app->singleton(AlertBag::class, static function ($app) {
            return $app->make('session')->get($app->make('config')->get('laralerts.key')) ?? new AlertBag;
        });

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
            // @codeCoverageIgnoreStart
            $blade->directive($app->make('config')->get('laralerts.directive'), function () use ($app) {
                return "<?php echo \$__env->make('laralerts::alerts', [], ['alerts' => app(\DarkGhostHunter\Laralerts\AlertBag::class)->getAlerts()])->render(); ?>";
            });
            // @codeCoverageIgnoreEnd
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