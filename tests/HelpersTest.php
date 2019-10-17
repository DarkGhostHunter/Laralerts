<?php

namespace DarkGhostHunter\Laralerts\Tests;

use BadMethodCallException;
use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertManager;
use DarkGhostHunter\Laralerts\Testing\WithAlerts;

class HelpersTest extends TestCase
{
    use Concerns\RegistersPackage, WithAlerts;

    public function testResolvesAlertFactory()
    {
        $this->assertInstanceOf(AlertManager::class, alert());
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

    public function testAlertIf()
    {
        $false = alert_if(false, 'message')->setType('success')->setClasses('test_class')->setDismiss(true);
        $this->assertDoesntHaveAlerts();

        $true = alert_if(true, 'message')->setType('success')->setClasses('test_class')->setDismiss(true);
        $this->assertHasAnyAlert();

        $this->assertInstanceOf(Alert::class, $true);
        $this->assertInstanceOf(Alert::class, $false);
    }

    public function testAlertUnless()
    {
        $false = alert_unless(true, 'message')->setType('success')->setClasses('test_class')->setDismiss(true);
        $this->assertDoesntHaveAlerts();

        $true = alert_unless(false, 'message')->setType('success')->setClasses('test_class')->setDismiss(true);
        $this->assertHasAnyAlert();

        $this->assertInstanceOf(Alert::class, $false);
        $this->assertInstanceOf(Alert::class, $true);
    }
}
