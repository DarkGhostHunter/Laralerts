<?php

namespace DarkGhostHunter\Laralerts\Tests;

use Closure;
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
        /** @var \Illuminate\View\Factory $factory */
        $factory = $this->app->make('view');
        $factory->addLocation(__DIR__ . '/');

        AlertFacade::message('foo');
        AlertFacade::message('bar');

        $rendered = $factory->make('test-view')->render();

        $this->assertStringContainsString('alerts', $rendered);
        $this->assertStringContainsString('foo', $rendered);
        $this->assertStringContainsString('bar', $rendered);
    }

    public function testCompilesToEmptyString()
    {
        /** @var \Illuminate\View\Factory $factory */
        $factory = $this->app->make('view');
        $factory->addLocation(__DIR__ . '/');
        $rendered = $factory->make('test-view')->render();

        $this->assertEmpty($rendered);
    }
}
