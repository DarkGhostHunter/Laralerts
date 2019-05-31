<?php

namespace DarkGhostHunter\Laralerts\Facades;

use DarkGhostHunter\Laralerts\AlertFactory;
use Illuminate\Support\Facades\Facade;

/**
 * Class Alert
 * @method static string getKey
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setKey(string $key)
 * @method static string getDefaultAnimationClass()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setDefaultAnimationClass()
 * @method static \DarkGhostHunter\Laralerts\AlertBag getAlertBag()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setAlertBag(\DarkGhostHunter\Laralerts\AlertBag $alertBag)
 * @method static string getDefaultType()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setDefaultType(string $type)
 * @method static array getDefaultClasses()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setDefaultClasses(...$classes)
 * @method static bool isDefaultShow()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setDefaultShow(bool $show)
 * @method static bool isDefaultDismissible()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory setDefaultDismissible(bool $dismissible)
 * @method static \DarkGhostHunter\Laralerts\Alert add()
 * @method static \DarkGhostHunter\Laralerts\AlertFactory putAlertBag(\DarkGhostHunter\Laralerts\Alert $alert)
 * @method static \DarkGhostHunter\Laralerts\Alert make()
 *
 * @method static \DarkGhostHunter\Laralerts\Alert message(string $text)
 * @method static \DarkGhostHunter\Laralerts\Alert lang(string $key)
 * @method static \DarkGhostHunter\Laralerts\Alert dismissible(bool $show = true, string $animationClass = 'fade')
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
