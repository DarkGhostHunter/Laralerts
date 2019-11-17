<?php

namespace DarkGhostHunter\Laralerts;

use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Session\Store;
use Illuminate\Support\Traits\Macroable;

/**
 * Class AlertManager
 *
 * @package DarkGhostHunter\Laralerts
 *
 */
class AlertManager
{
    use Concerns\AlertManager\HasGettersAndSetters,
        Macroable {
        __call as macroCall;
    }

    /**
     * Session Store
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * The actual Alert Bag
     *
     * @var \DarkGhostHunter\Laralerts\AlertBag
     */
    protected $alertBag;

    /**
     * Session Key to handle the Alert Bag
     *
     * @var string
     */
    protected $key;

    /**
     * Default Type of the Alerts to make
     *
     * @var string
     */
    protected $type;

    /**
     * The Default dismiss behaviour of the Alerts to make
     *
     * @var mixed
     */
    protected $dismiss;

    /**
     * Manager constructor.
     *
     * @param  \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @param  \Illuminate\Session\Store $session
     * @param  string $key
     * @param  string $type
     * @param  bool $dismiss
     */
    public function __construct(AlertBag $alertBag,
                                Store $session,
                                string $key,
                                ?string $type,
                                bool $dismiss)
    {
        $this->session = $session;
        $this->alertBag = $alertBag;
        $this->key = $key;
        $this->type = $type;
        $this->dismiss = $dismiss;
    }

    /**
     * Keeps the Alert Bag for another Request
     *
     * @return $this
     */
    public function reflash()
    {
        $this->alertBag->reflash();

        if ($this->session->isStarted()) {
            $this->session->keep($this->key);
        }

        return $this;
    }

    /**
     * Adds an Alert from a JSON string
     *
     * @param  string $json
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function addFromJson(string $json)
    {
        return $this->add(Alert::fromJson($json));
    }

    /**
     * Adds an Alert and returns the same added Alert.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert $alert
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function add(Alert $alert)
    {
        $this->retrieveAlertBag()->add($alert);

        return $alert;
    }

    /**
     * Retrieves a new Alert Bag or the old, depending if it should be kept
     *
     * @return \DarkGhostHunter\Laralerts\AlertBag
     */
    protected function retrieveAlertBag()
    {
        // Ensure the Alert Bag is in the session if it hasn't been already flashed into it.
        return $this->flashBagInSession()->alertBag;
    }

    /**
     * Flash an Alert Bag into the Session Store if it was not flashed before
     *
     * @return $this
     */
    protected function flashBagInSession()
    {
        if ($this->session->isStarted() && ! $this->session->has($this->key)) {
            $this->session->flash($this->key, $this->alertBag);
        }

        return $this;
    }

    /**
     * Adds an Alert from an array
     *
     * @param  array $attributes
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function addFromArray(array $attributes)
    {
        return $this->add(Alert::fromArray($attributes));
    }

    /**
     * Add many alerts from an Array. Returns the number of alerts added.
     *
     * @param  array $alerts
     * @param  string|null $location
     * @return \DarkGhostHunter\Laralerts\AlertManager
     */
    public function addManyFromArray(array $alerts, string $location = null)
    {
        $alerts = Arr::get($alerts, $location, $alerts);

        foreach ($alerts as $alert) {
            $this->addFromArray($alert);
        }

        return $this;
    }

    /**
     * Add many Alerts from a JSON string
     *
     * @param  string $json
     * @param  string|null $location
     * @return \DarkGhostHunter\Laralerts\AlertManager
     */
    public function addManyFromJson(string $json, string $location = null)
    {
        $this->addManyFromArray(json_decode($json, true), $location);

        return $this;
    }

    /**
     * Makes a new Alert instance
     *
     * @param  string|null $message
     * @param  string|null $type
     * @param  bool|null $dismiss
     * @param  string|null $classes
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function make(string $message = null,
                         string $type = null,
                         bool $dismiss = null,
                         string $classes = null)
    {
        return new Alert($message, $type ?? $this->type, $dismiss ?? $this->dismiss, $classes);
    }

    /**
     * Gracefully pass the call to a new Alert instance
     *
     * @param $method
     * @param $parameters
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if ((method_exists(Alert::class, $method) && is_callable([Alert::class, $method]))) {
            return $this->add($this->make())->{$method}(...$parameters);
        }

        if (isset(Alert::getTypes()[$method])) {
            return $this->add($this->make())->setType($method);
        }

        throw new BadMethodCallException("Method $method does not exist.");
    }
}
