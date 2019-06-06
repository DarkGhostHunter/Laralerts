<?php

namespace DarkGhostHunter\Laralerts\Tests;

use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\AlertBuilder;
use DarkGhostHunter\Laralerts\AlertFactory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Config;
use Mockery;
use Orchestra\Testbench\TestCase;

class AlertBuilderTest extends TestCase
{
    use Concerns\RegistersPackage;


    /** @var \Illuminate\Session\Store & \Mockery\MockInterface */
    protected $mockStore;

    /** @var \DarkGhostHunter\Laralerts\AlertBag & \Mockery\MockInterface */
    protected $mockBag;

    /** @var \Illuminate\Contracts\Config\Repository & \Mockery\MockInterface */
    protected $mockConfig;

    /** @var \Illuminate\Contracts\Foundation\Application & \Mockery\MockInterface */
    protected $mockApp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockStore = $this->mock(Store::class);
        $this->mockBag = $this->mock(AlertBag::class);
        $this->mockConfig = $this->mock(Repository::class);
        $this->mockApp = $this->mock(Application::class);
    }

    public function testBuilder()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('laralerts')
            ->andReturn([
                'directive' => 'foo',
                'key' => 'bar',
                'type' => 'baz',
                'dismiss' => true,
            ]);

        $mockStore = $this->mock(Store::class);

        $mockStore->shouldReceive('isStarted')
            ->once()
            ->andReturnTrue();

        $mockStore->shouldReceive('flash')
            ->with('bar', Mockery::type(AlertBag::class))
            ->andReturnNull();

        $this->app->instance(Store::class, $mockStore);

        $this->assertInstanceOf(AlertFactory::class, AlertBuilder::build($this->app));
    }

    public function testBuilderDoesntFlashWhenSessionHasNotStarted()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('laralerts')
            ->andReturn([
                'directive' => 'foo',
                'key' => 'bar',
                'type' => 'baz',
                'dismiss' => true,
            ]);

        $mockStore = $this->mock(Store::class);

        $mockStore->shouldReceive('isStarted')
            ->once()
            ->andReturnFalse();

        $mockStore->shouldNotReceive('flash');

        $this->app->instance(Store::class, $mockStore);

        $this->assertInstanceOf(AlertFactory::class, AlertBuilder::build($this->app));
    }
}