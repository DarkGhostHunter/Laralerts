<?php

namespace DarkGhostHunter\Laralerts\Tests\Http;

use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\Tests\Concerns\RegistersPackage;
use Illuminate\View\Compilers\BladeCompiler;
use Orchestra\Testbench\TestCase;

class AlertController extends TestCase
{
    use RegistersPackage;

    public function testRendersAlert()
    {
        config(['app.key' => bin2hex(random_bytes(16))]);

        $this->app->make('router')->get('test', function() {
            alert('foo');

            /** @var BladeCompiler $compiler */
            $compiler = app(BladeCompiler::class);

            return response($compiler->compileString('@alerts'));
        })->middleware('web');

        $this->get('test')->assertSessionHas('_alerts', $this->app->make(AlertBag::class));

    }
}