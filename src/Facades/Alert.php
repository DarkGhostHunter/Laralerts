<?php

namespace DarkGhostHunter\Laralerts\Facades;

use DarkGhostHunter\Laralerts\Bag;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DarkGhostHunter\Laralerts\Alert persistAs(string $key)
 * @method static \DarkGhostHunter\Laralerts\Alert message(string $message)
 * @method static \DarkGhostHunter\Laralerts\Alert raw(string $message)
 * @method static \DarkGhostHunter\Laralerts\Alert types(string ...$types)
 * @method static \DarkGhostHunter\Laralerts\Alert dismiss(bool $dismissible = true)
 * @method static \DarkGhostHunter\Laralerts\Alert links(string|array $name, string $value = null)
 * @method static \DarkGhostHunter\Laralerts\Alert when($condition)
 * @method static \DarkGhostHunter\Laralerts\Alert unless($condition)
 */
class Alert extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        return Bag::class;
    }
}
