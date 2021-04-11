<?php

namespace Tests\Http\Middleware;

use DarkGhostHunter\Laralerts\Http\Middleware\StorePersistentAlertsInSession;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;
use Tests\TestsView;

class StorePersistentAlertsInSessionTest extends TestCase
{
    use RegistersPackage;
    use TestsView;

    protected function registerRoutes(): void
    {
        $router = $this->app['router'];

        $router->get('foo')->uses(
            function () {
                alert('foo');
                return $this->view;
            }
        )->middleware('web');

        $router->get('bar')->uses(
            function () {
                alert('bar');
                return $this->view;
            }
        )->middleware('web');

        $router->get('empty')->uses(
            function () {
                alert()->message('');
                return $this->view;
            }
        )->middleware('web');

        $router->get('persist')->uses(
            function () {
                alert()->message('foo');
                alert()->message('foo')->persistAs('foo.bar');
                return $this->view;
            }
        )->middleware('web');

        $router->get('no-alert')->uses(
            function () {
                return $this->view;
            }
        )->middleware('web');
    }

    protected function setUp(): void
    {
        $this->afterApplicationCreated([$this, 'addTestView']);
        $this->afterApplicationCreated([$this, 'registerRoutes']);

        parent::setUp();
    }

    public function test_doesnt_stores_persistent_without_session()
    {
        $this->app['router']->get('no-session')->uses(
            function () {
                alert()->message('foo')->persistAs('foo.bar');
                return $this->view;
            }
        )->middleware(StorePersistentAlertsInSession::class);

        $this->get('no-session')->assertSessionMissing('_alerts');
    }

    public function test_doesnt_renders_empty_alerts()
    {
        $response = $this->get('empty')->assertSessionMissing('_alerts');

        static::assertEquals(
            <<<'VIEW'
<div class="container">
    </div>

VIEW
            ,
            $response->getContent()
        );
    }

    public function test_renders_alert_one_time()
    {
        $response = $this->get('foo')->assertSessionMissing('_alerts');

        static::assertEquals(
            <<<'VIEW'
<div class="container">
    <div class="alerts">
        <div class="alert" role="alert">
    foo
    </div>
    </div>
</div>

VIEW
            ,
            $response->getContent()
        );

        $this->refreshApplication();
        $this->setUp();

        $response = $this->get('empty')->assertSessionMissing('_alerts');

        static::assertEquals(
            <<<'VIEW'
<div class="container">
    </div>

VIEW
            ,
            $response->getContent()
        );
    }

    public function test_persists_alerts_through_session()
    {
        $response = $this->get('persist')->assertSessionHas('_alerts');

        static::assertEquals(
            <<<'VIEW'
<div class="container">
    <div class="alerts">
        <div class="alert" role="alert">
    foo
    </div>
<div class="alert" role="alert">
    foo
    </div>
    </div>
</div>

VIEW
            ,
            $response->getContent()
        );

        $session = $this->app['session']->all();

        $this->refreshApplication();
        $this->setUp();

        $this->session($session);

        $response = $this->get('empty')->assertSessionHas('_alerts');

        static::assertEquals(
            <<<'VIEW'
<div class="container">
    <div class="alerts">
        <div class="alert" role="alert">
    foo
    </div>
    </div>
</div>

VIEW
            ,
            $response->getContent()
        );
    }
}
