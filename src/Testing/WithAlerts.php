<?php

namespace DarkGhostHunter\Laralerts\Testing;

use PHPUnit\Framework\Assert as PHPUnit;
use DarkGhostHunter\Laralerts\AlertManager;

/**
 * Trait WithAlerts
 *
 * @package DarkGhostHunter\Laralerts\Testing
 */
trait WithAlerts
{
    /**
     * Assert that the Alert Bag has any Alert
     *
     * @return void
     */
    public function assertHasAnyAlert()
    {
        $bag = $this->app[AlertManager::class]->getAlertBag();

        PHPUnit::assertNotEmpty($bag->getAlerts(), 'The Alert Bag is empty');
    }

    /**
     * Asset that the Alert Bag is empty
     *
     * @return void
     */
    public function assertDoesntHaveAlerts()
    {
        $bag = $this->app[AlertManager::class]->getAlertBag();

        PHPUnit::assertEmpty($bag->getAlerts(), 'The Alert Bag is not empty');
    }

    /**
     * Assert that the Alert Bag has a particular Alert
     *
     * @param  string|null $message
     * @param  string|null $type
     * @return void
     */
    public function assertHasAlert(string $message, string $type = null)
    {
        $bag = $this->app[AlertManager::class]->getAlertBag();

        PHPUnit::assertNotEmpty(
            $bag->filterByMessage($message, $type),
            "No Alert with [$message] " . ($type ? "and type [$type]" : '') . 'was found.'
        );
    }

    /**
     * Assert the Alert Bag doesn't have an Alert
     *
     * @param  string|null $message
     * @param  string|null $type
     */
    public function assertDoesntHaveAlert(string $message, string $type = null)
    {
        $bag = $this->app[AlertManager::class]->getAlertBag();

        PHPUnit::assertEmpty(
            $bag->filterByMessage($message, $type),
            "An Alert with [$message] " . ($type ? "and type [$type]" : '') . 'was found.'
        );
    }

    /**
     * Assert that the Alert Bag contains a given number of Alerts
     *
     * @param  int $expected
     * @return void
     */
    public function assertAlertsCount(int $expected)
    {
        $bag = $this->app[AlertManager::class]->getAlertBag();

        $actual = $bag->getAlerts();

        $count = count($actual);

        PHPUnit::assertCount(
            $expected, $actual,
            "The Alert Bag has $count Alerts instead of $expected"
        );
    }
}
