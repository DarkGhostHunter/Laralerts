<?php

namespace DarkGhostHunter\Laralerts;

use BadMethodCallException;
use Illuminate\Session\Store as Session;

class AlertFactory
{
    use Concerns\HasDefaults;
    /**
     * Key to use in the Session to identify the AlertBag
     *
     * @var string
     */
    protected $key = 'alerts';

    /**
     * Alert Bag
     *
     * @var \DarkGhostHunter\Laralerts\AlertBag
     */
    protected $alertBag;

    /**
     * Session handler
     *
     * @var \Illuminate\Contracts\Session\Session
     */
    protected $session;

    /**
     * AlertFactory constructor.
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @param \Illuminate\Session\Store $session
     */
    public function __construct(AlertBag $alertBag, Session $session)
    {
        $this->alertBag = $alertBag;
        $this->session = $session;
    }

    /**
     * Return the Key to put in the Session for the AlertBag
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the Key to put in the Session for the AlertBag
     *
     * @param string $key
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }


    /**
     * Return the current Alert Bag
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
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function setAlertBag(AlertBag $alertBag)
    {
        $this->alertBag = $alertBag;

        return $this;
    }

    /**
     * Creates and adds a new Alert
     *
     * @param \DarkGhostHunter\Laralerts\Alert|null $alert
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function add(Alert $alert = null)
    {
        $alert = $alert ?? $this->make();

        $this->alertBag->add($alert);

        $this->putAlertBag($this->alertBag);

        return $alert;
    }

    /**
     * Puts the AlertBag into the Session
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function putAlertBag(AlertBag $alertBag)
    {
        $this->session->flash($this->key, $alertBag);

        return $this;
    }

    /**
     * Creates a new Alert
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function make()
    {
        $alert = (new Alert)
            ->classes($this->defaultClasses);

        if ($this->defaultDismissible) {
            $alert->dismissible($this->defaultShow, $this->defaultAnimationClass);
        }

        if ($this->defaultType) {
            $alert->{$this->defaultType}();
        }

        return $alert;
    }

    /**
     * If the call can be called to the Alert instance, we create a new one.
     *
     * @param $name
     * @param $arguments
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (is_callable([Alert::class, $name])) {
            return $this->add()->{$name}(...$arguments);
        }

        throw new BadMethodCallException("Method $name does not exist.");
    }

}