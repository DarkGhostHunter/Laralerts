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
     * Creates a new Alert into this Bag instance.
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function new(): Alert
    {
        $this->add($alert = new Alert());

        return $alert;
    }

    /**
     * Adds an Alert from an array.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     */
    public function add(Alert $alert)
    {
        $this->alerts[] = $alert;
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
     * Returns all alerts that are marked as persistent.
     *
     * @return array|\DarkGhostHunter\Laralerts\Alert[]
     */
    public function allPersistent(): array
    {
        return array_filter(
            $this->alerts,
            static function (Alert $alert) {
                return $alert->isPersistent();
            }
        );
    }

    /**
     * Ensures an Alert is only persisted once by its key.
     *
     * @param  string  $key
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function unique(string $key): Alert
    {
        foreach ($this->alerts as $alert) {
            if ($alert->getPersistKey() === $key) {
                return $alert;
            }
        }

        return $this->new()->persistAs($key);
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
    public function hasPersistent(string $key): bool
    {
        foreach ($this->alerts as $alert) {
            if ($alert->getPersistKey() === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Creates an Alert only if the condition evaluates to true.
     *
     * @param  callable|int|bool  $condition
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function when($condition): Alert
    {
        if (value($condition)) {
            return $this->new();
        }

        return new Alert();
    }

    /**
     * Creates an Alert only if the condition evaluates to false.
     *
     * @param  callable|int|bool  $condition
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function unless($condition): Alert
    {
        if (!value($condition)) {
            return $this->new();
        }

        return new Alert();
    }

    /**
     * Adds an Alert into the bag from a JSON string.
     *
     * @param  string  $alert
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \JsonException
     */
    public function fromJson(string $alert): Alert
    {
        $alert = Alert::fromArray(json_decode($alert, true, 512, JSON_THROW_ON_ERROR));

        $this->add($alert);

        return $alert;
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
        return $this->new()->{$name}(...$arguments);
    }
}
