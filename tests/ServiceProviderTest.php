<?php

namespace DarkGhostHunter\Laralerts\Tests;

use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laralerts\AlertManager;
use DarkGhostHunter\Laralerts\Facades\Alert;
use DarkGhostHunter\Laralerts\LaralertsServiceProvider;

class ServiceProviderTest extends TestCase
{
    use Concerns\RegistersPackage;

    public function testRegistersPackage()
    {
        $this->assertArrayHasKey(LaralertsServiceProvider::class, $this->app->getLoadedProviders());
    }

    public function testFacades()
    {
        $this->assertInstanceOf(AlertManager::class, Alert::getFacadeRoot());
        $this->assertInstanceOf(\DarkGhostHunter\Laralerts\Alert::class, Alert::make());
    }
}
