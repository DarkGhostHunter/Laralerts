<?php

namespace Tests;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use Orchestra\Testbench\TestCase;

class HelpersTest extends TestCase
{
    use RegistersPackage;

    public function test_resolves_alert_factory(): void
    {
        static::assertInstanceOf(Bag::class, alert());
    }

    public function test_creates_alert(): void
    {
        $alert = alert('test-message', 'info');
        static::assertInstanceOf(Alert::class, $alert);
        static::assertEquals([
            'message' => 'test-message',
            'types' => ['info'],
            'dismissible' => false,
        ], $alert->toArray());
    }
}
