<?php

namespace DarkGhostHunter\Laralerts\Tests\Http;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Session;
use DarkGhostHunter\Laralerts\AlertBag;
use Illuminate\View\Compilers\BladeCompiler;
use DarkGhostHunter\Laralerts\Tests\Concerns\RegistersPackage;

class AlertController extends TestCase
{
    use RegistersPackage;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.key' => bin2hex(random_bytes(16))]);

        $this->app->make('router')->get('foo', function() {
            alert('foo');
            return response(app(BladeCompiler::class)->compileString('@alerts'));
        })->middleware('web');

        $this->app->make('router')->get('bar', function() {
            alert('bar');
            return response(app(BladeCompiler::class)->compileString('@alerts'));
        })->middleware('web');
    }

    public function testRendersAlert()
    {
        $this->get('foo')->assertSessionHas('_alerts', $this->app->make(AlertBag::class));

        $this->refreshApplication();
        $this->setUp();

        $this->get('bar')->assertSessionHas('_alerts', $this->app->make(AlertBag::class));
    }

    public function testRefreshesAlertBagsBetweenRequests()
    {
        $this->get('foo')->assertSessionHas('_alerts', $this->app->make(AlertBag::class));
        $this->assertEquals('foo', Session::get('_alerts')->getOld()[0]->getMessage());

        $this->refreshApplication();
        $this->setUp();

        $this->get('bar')->assertSessionHas('_alerts', $this->app->make(AlertBag::class));
        $this->assertCount(1, Session::get('_alerts')->getOld());
        $this->assertCount(0, Session::get('_alerts')->getAlerts());
    }
}
