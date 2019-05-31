<?php

namespace DarkGhostHunter\Laralerts\Tests;

use Closure;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\Facades\Alert as AlertFacade;
use Orchestra\Testbench\TestCase;

class BladeDirectiveTest extends TestCase
{
    use Concerns\RegistersPackage;

    public function testRegistersDirective()
    {
        /** @var \Illuminate\View\Compilers\BladeCompiler $compiler */
        $compiler = $this->app->make('blade.compiler');

        $this->assertArrayHasKey('alerts', $compiler->getCustomDirectives());
        $this->assertInstanceOf(Closure::class, $compiler->getCustomDirectives()['alerts']);
    }

    public function testCompilesToHTML()
    {
        /** @var \Illuminate\View\Compilers\BladeCompiler $compiler */
        $compiler = $this->app->make('blade.compiler');

        AlertFacade::message('test-message');

        $this->assertEquals('<div class="alert" role="alert">test-message</div>', $compiler->compileString('@alerts'));
    }

    public function testCompilesToEmptyString()
    {
        /** @var \Illuminate\View\Compilers\BladeCompiler $compiler */
        $compiler = $this->app->make('blade.compiler');

        $this->assertEmpty($compiler->compileString('@alerts'));
    }
}
