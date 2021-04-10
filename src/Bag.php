<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Session\Session;

class Bag
{
    /**
     * Alerts that will be rendered into the view.
     *
     * @var array|\DarkGhostHunter\Laralerts\Alert[]
     */
    protected array $alerts = [];

    /**
     * Current Session.
     *
     * @var \Illuminate\Contracts\Session\Session
     */
    protected Session $session;

    /**
     * Application config.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected Repository $config;

    /**
     * Bag constructor.
     *
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  \Illuminate\Contracts\Config\Repository  $config
     */
    public function __construct(Session $session, Repository $config)
    {
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * Returns all alerts.
     *
     * @return array|\DarkGhostHunter\Laralerts\Alert[]
     */
    public function all(): array
    {
        return $this->alerts;
    }

    /**
     * Returns the Alerts that will be rendered.
     *
     * @return array|\DarkGhostHunter\Laralerts\Alert[]
     */
    public function getPersistentAlerts(): array
    {
        return array_filter($this->alerts, static function (Alert $alert) {
            return $alert->isPersistent();
        });
    }

    /**
     * Creates a new Alert, adds it to this bag, and injects the current Bag instance.
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function create(): Alert
    {
        return new Alert($this);
    }

    /**
     * Adds an Alert to the Alert Bag, returns the ID of the Alert.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function add(Alert $alert): Alert
    {
        $this->alerts[] = $alert;

        return $alert;
    }

    /**
     * Abandons a persistent Alert. Returns true if successful.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function abandon(string $key): bool
    {
        foreach ($this->alerts as $index => $alert) {
            if ($alert->getPersistKey() === $key) {
                unset($this->alerts[$index]);
                return true;
            }
        }

        ksort($this->alerts);

        return false;
    }

    /**
     * Deletes all alerts.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->alerts = [];
    }

    /**
     * Check if an Alert by the given key is persistent.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function isPersistent(string $key): bool
    {
        foreach ($this->alerts as $alert) {
            if ($alert->getPersistKey() === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Pass through all calls to the Alert method.
     *
     * @param  string  $name
     * @param  array  $arguments
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function __call(string $name, array $arguments)
    {
        return $this->create()->{$name}(...$arguments);
    }
}
