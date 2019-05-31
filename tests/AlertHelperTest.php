<?php

namespace DarkGhostHunter\Laralerts\Tests;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertFactory;
use Orchestra\Testbench\TestCase;

class AlertHelperTest extends TestCase
{
    use Concerns\RegistersPackage;

    public function testResolvesAlertFactory()
    {
        $this->assertInstanceOf(AlertFactory::class, alert());
    }

    public function testCreatesAlert()
    {
        $this->assertInstanceOf(Alert::class, alert('test-message'));
    }
}