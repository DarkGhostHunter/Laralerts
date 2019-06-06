<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\Store;

class AlertBuilder
{
    /**
     * Creates a new Alert Factory and prepares de Alert
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function build(Application $app)
    {
        [
            'key' => $key,
            'type' => $type,
            'dismiss' => $dismiss,
        ]
            = $app->make('config')->get('laralerts');

        return self::setAlertBag(
            new AlertFactory($app->make(AlertBag::class), $app->make(Store::class), $type, $dismiss),
            $key
        );
    }

    /**
     * Flash the Alert Bag if needed
     *
     * @param \DarkGhostHunter\Laralerts\AlertFactory $factory
     * @param string $key
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    protected static function setAlertBag(AlertFactory $factory, string $key)
    {
        $store = $factory->getStore();

        if ($store->isStarted()) {
            $store->flash($key, $factory->getAlertBag());
        }

        return $factory;
    }
}