<?php

namespace DarkGhostHunter\Laralerts\Tests;

use DarkGhostHunter\Laralerts\Facades\Alert;
use DarkGhostHunter\Laralerts\AlertFactory;
use DarkGhostHunter\Laralerts\LaralertsServiceProvider;
use DarkGhostHunter\Laralerts\Tests\Concerns\RegistersPackage;
use Orchestra\Testbench\TestCase;

class ServiceProviderTest extends TestCase
{
    use Concerns\RegistersPackage;

    public function testRegistersPackage()
    {
        $this->assertArrayHasKey(LaralertsServiceProvider::class, $this->app->getLoadedProviders());
    }

    public function testFacades()
    {
        $this->assertInstanceOf(\DarkGhostHunter\Laralerts\AlertFactory::class, Alert::getFacadeRoot());
        $this->assertInstanceOf(\DarkGhostHunter\Laralerts\Alert::class, Alert::make());
    }
}
