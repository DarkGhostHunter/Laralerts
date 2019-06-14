<?php

namespace DarkGhostHunter\Laralerts\Tests\Http;

use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\Tests\Concerns\RegistersPackage;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Compilers\BladeCompiler;
use Orchestra\Testbench\TestCase;

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
        $this->get('bar')->assertSessionHas('_alerts', $this->app->make(AlertBag::class));
    }

    public function testRefreshesAlertBagsBetweenRequests()
    {
        $this->get('foo')->assertSessionHas('_alerts', $this->app->make(AlertBag::class));
        $this->assertEquals('foo', Session::get('_alerts')->getAlerts()[0]->getMessage());

        $this->refreshApplication();
        $this->setUp();

        $this->get('bar')->assertSessionHas('_alerts', $this->app->make(AlertBag::class));
        $this->assertCount(1, Session::get('_alerts'));
    }
}