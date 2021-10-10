<?php

namespace Tests;

use DarkGhostHunter\Laralerts\Bag;
use DarkGhostHunter\Laralerts\Facades\Alert;
use DarkGhostHunter\Laralerts\Http\Middleware\StoreAlertsInSession;
use DarkGhostHunter\Laralerts\LaralertsServiceProvider;
use DarkGhostHunter\Laralerts\View\Component\LaralertsComponent;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\View\Compilers\BladeCompiler;
use Orchestra\Testbench\TestCase;

class ServiceProviderTest extends TestCase
{
    use RegistersPackage;

    public function test_registers_package(): void
    {
        static::assertArrayHasKey(LaralertsServiceProvider::class, $this->app->getLoadedProviders());
    }

    public function test_facades(): void
    {
        static::assertInstanceOf(Bag::class, Alert::getFacadeRoot());
        static::assertInstanceOf(\DarkGhostHunter\Laralerts\Alert::class, Alert::new());
    }

    public function test_uses_config(): void
    {
        static::assertEquals(include(__DIR__ . '/../config/laralerts.php'), config('laralerts'));
    }

    public function test_publishes_config(): void
    {
        $this->artisan(
            'vendor:publish',
            [
                '--provider' => 'DarkGhostHunter\Laralerts\LaralertsServiceProvider',
                '--tag' => 'config',
            ]
        )->execute();

        $this->assertFileExists(base_path('config/laralerts.php'));
        $this->assertFileEquals(base_path('config/laralerts.php'), __DIR__ . '/../config/laralerts.php');

        unlink(base_path('config/laralerts.php'));
    }

    public function test_uses_views(): void
    {
        $view = $this->app->make(Factory::class);

        static::assertTrue($view->exists('laralerts::bootstrap.alert'));
        static::assertTrue($view->exists('laralerts::bootstrap.container'));
    }

    public function test_publishes_views(): void
    {
        $this->artisan(
            'vendor:publish',
            [
                '--provider' => 'DarkGhostHunter\Laralerts\LaralertsServiceProvider',
                '--tag' => 'views',
            ]
        )->execute();

        static::assertFileEquals(
            resource_path('views/vendor/laralerts/bootstrap/container.blade.php'),
            __DIR__ . '/../resources/views/bootstrap/container.blade.php'
        );

        static::assertFileEquals(
            resource_path('views/vendor/laralerts/bootstrap/alert.blade.php'),
            __DIR__ . '/../resources/views/bootstrap/alert.blade.php'
        );

        unlink(resource_path('views/vendor/laralerts/bootstrap/alert.blade.php'));
        unlink(resource_path('views/vendor/laralerts/bootstrap/container.blade.php'));
    }

    public function test_published_component(): void
    {
        $aliases = $this->app->make(BladeCompiler::class)->getClassComponentAliases();

        static::assertArrayHasKey('laralerts', $aliases);
        static::assertEquals(LaralertsComponent::class, $aliases['laralerts']);
    }

    public function test_registers_middleware(): void
    {
        $kernel = $this->app->make(Kernel::class);

        static::assertEquals(StoreAlertsInSession::class, Arr::last($kernel->getMiddlewareGroups()['web']));

        $router = $this->app->make(Router::class);

        static::assertArrayHasKey('laralerts.json', $router->getMiddleware());
    }
}
