<?php

namespace DarkGhostHunter\Laralerts\Facades;

use Closure;
use DarkGhostHunter\Laralerts\Bag;
use DarkGhostHunter\Laralerts\Testing\Fakes\BagFake;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection<\DarkGhostHunter\Laralerts\Alert> collect()
 * @method static \DarkGhostHunter\Laralerts\Alert persistAs(string $key)
 * @method static bool abandon(string $key)
 * @method static bool hasPersistent(string $key)
 * @method static void flush()
 * @method static \DarkGhostHunter\Laralerts\Alert message(string $message)
 * @method static \DarkGhostHunter\Laralerts\Alert raw(string $message)
 * @method static \DarkGhostHunter\Laralerts\Alert types(string ...$types)
 * @method static \DarkGhostHunter\Laralerts\Alert dismiss(bool $dismissible = true)
 * @method static \DarkGhostHunter\Laralerts\Alert when(Closure|bool $condition)
 * @method static \DarkGhostHunter\Laralerts\Alert unless(Closure|bool $condition)
 * @method static \DarkGhostHunter\Laralerts\Alert away(string $replace, string $url, bool $blank = true)
 * @method static \DarkGhostHunter\Laralerts\Alert to(string $replace, string $url, bool $blank = false)
 * @method static \DarkGhostHunter\Laralerts\Alert route(string $replace, string $name, array $parameters = [], bool $blank = false)
 * @method static \DarkGhostHunter\Laralerts\Alert action(string $replace, string|array $action, array $parameters = [], bool $blank = false)
 * @method static \DarkGhostHunter\Laralerts\Alert tags(string ...$tags)
 * @method static \DarkGhostHunter\Laralerts\Alert fromJson(string $alert, int $options = 0)
 */
class Alert extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Bag::class;
    }

    /**
     * Creates a fake Alert Bag.
     *
     * @return \DarkGhostHunter\Laralerts\Testing\Fakes\BagFake
     */
    public static function fake(): BagFake
    {
        $fake = static::$app->make(BagFake::class, [
            'tags' => Arr::wrap(Config::get('laralerts.tags'))
        ]);

        static::swap($fake);

        return $fake;
    }
}
