<?php

namespace DarkGhostHunter\Laralerts;

use BadMethodCallException;
use Illuminate\Session\Store as Session;

class AlertFactory
{
    /**
     * Alert Bag
     *
     * @var \DarkGhostHunter\Laralerts\AlertBag
     */
    protected $alertBag;

    /**
     * Session Store
     *
     * @var \Illuminate\Session\Store
     */
    protected $store;

    /**
     * The default type for the Alerts
     *
     * @var string
     */
    protected $type;

    /**
     * If the Alerts should be dismissible by default
     *
     * @var bool
     */
    protected $dismiss;

    /**
     * Creates a new Alert Factory instance
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @param \Illuminate\Session\Store $session
     * @param string $type
     * @param bool $dismiss
     */
    public function __construct(AlertBag $alertBag, Session $session, ?string $type, bool $dismiss)
    {
        $this->alertBag = $alertBag;
        $this->store = $session;
        $this->type = $type;
        $this->dismiss = $dismiss;
    }

    /**
     * Return the Alert Bag
     *
     * @return \DarkGhostHunter\Laralerts\AlertBag
     */
    public function getAlertBag()
    {
        return $this->alertBag;
    }

    /**
     * Set the Alert Bag
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alertBag
     */
    public function setAlertBag(AlertBag $alertBag)
    {
        $this->alertBag = $alertBag;
    }

    /**
     * Return the Session Store
     *
     * @return \Illuminate\Session\Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set the Session Store
     *
     * @param \Illuminate\Session\Store $store
     */
    public function setStore(Session $store)
    {
        $this->store = $store;
    }

    /**
     * Adds an Alert and returns the same added Alert.
     *
     * @param \DarkGhostHunter\Laralerts\Alert $alert
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function add(Alert $alert)
    {
        $this->alertBag->add($alert);

        return $alert;
    }

    /**
     * Makes a new Alert instance
     *
     * @param string|null $message
     * @param string|null $type
     * @param bool|null $dismiss
     * @param string|null $classes
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function make(string $message = null, string $type = null, bool $dismiss = null, string $classes = null)
    {
        return new Alert($message, $type ?? $this->type, $dismiss ?? $this->dismiss, $classes);
    }

    /**
     * Gracefully pass the call to a new Alert instance
     *
     * @param $name
     * @param $arguments
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (is_callable([Alert::class, $name]) || in_array($name, Alert::getTypes(), false)) {
            return $this->add($this->make())->{$name}(...$arguments);
        }

        throw new BadMethodCallException("Method $name does not exist.");
    }
}