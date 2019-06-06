<?php

namespace DarkGhostHunter\Laralerts\Tests;

use BadMethodCallException;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertFactory;
use Orchestra\Testbench\TestCase;

class HelpersTest extends TestCase
{
    use Concerns\RegistersPackage;

    public function testResolvesAlertFactory()
    {
        $this->assertInstanceOf(AlertFactory::class, alert());
    }

    public function testCreatesAlert()
    {
        $alert = alert('test-message', 'info', true);
        $this->assertInstanceOf(Alert::class, $alert);
        $this->assertEquals([
            'message' => 'test-message',
            'type' => 'info',
            'dismiss' => true,
            'classes' => null,
        ], $alert->toArray());
    }

    public function testExceptionOnInvalidType()
    {
        $this->expectException(BadMethodCallException::class);

        alert('test-message', 'invalid-type', true);
    }
}