<?php

namespace DarkGhostHunter\Laralerts\Facades;

use DarkGhostHunter\Laralerts\AlertFactory;
use Illuminate\Support\Facades\Facade;

/**
 * Class Alert
 * @method static string getKey()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setKey(string $key)
 * @method static \DarkGhostHunter\Laralerts\AlertBag getAlertBag()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setAlertBag(\DarkGhostHunter\Laralerts\AlertBag $alertBag)
 * @method static \Illuminate\Session\Store getStore()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setStore(\Illuminate\Session\Store $alertBag)
 * @method static \DarkGhostHunter\Laralerts\Alert add(\DarkGhostHunter\Laralerts\Alert $alert)
 * @method static \DarkGhostHunter\Laralerts\Alert make(string $message = null, string $type = null, bool $dismiss = null, string $classes = null)
 *
 * @method static \DarkGhostHunter\Laralerts\Alert message(string $text)
 * @method static \DarkGhostHunter\Laralerts\Alert raw(string $text)
 * @method static \DarkGhostHunter\Laralerts\Alert lang(string $key)
 * @method static \DarkGhostHunter\Laralerts\Alert dismiss()
 * @method static \DarkGhostHunter\Laralerts\Alert fixed()
 * @method static \DarkGhostHunter\Laralerts\Alert primary()
 * @method static \DarkGhostHunter\Laralerts\Alert secondary()
 * @method static \DarkGhostHunter\Laralerts\Alert success()
 * @method static \DarkGhostHunter\Laralerts\Alert danger()
 * @method static \DarkGhostHunter\Laralerts\Alert warning()
 * @method static \DarkGhostHunter\Laralerts\Alert info()
 * @method static \DarkGhostHunter\Laralerts\Alert light()
 * @method static \DarkGhostHunter\Laralerts\Alert dark()
 * @method static \DarkGhostHunter\Laralerts\Alert classes(...$classes)
 *
 * @see \DarkGhostHunter\Laralerts\AlertFactory
 */
class Alert extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return AlertFactory::class;
    }
}
