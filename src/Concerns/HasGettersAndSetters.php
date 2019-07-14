<?php

namespace DarkGhostHunter\Laralerts\Concerns;

use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\AlertManager;
use Illuminate\Session\Store;

trait HasGettersAndSetters
{
    /**
     * Return the Session Store
     *
     * @return \Illuminate\Session\Store
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Sets the Session Store
     *
     * @param \Illuminate\Session\Store $session
     * @return AlertManager
     */
    public function setSession(Store $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Return the current Alert Bag being used
     *
     * @return \DarkGhostHunter\Laralerts\AlertBag
     */
    public function getAlertBag()
    {
        return $this->alertBag;
    }

    /**
     * Sets the Alert Bag to use
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @return AlertManager
     */
    public function setAlertBag(AlertBag $alertBag)
    {
        $this->alertBag = $alertBag;

        return $this;
    }

    /**
     * Return the Key to store the Alert Bag in the Session Store
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the Key to store the Alert Bag in the Session Store
     *
     * @param string $key
     * @return AlertManager
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Return the Type to use with the new Alerts
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the Type to use with the new Alerts
     *
     * @param string $type
     * @return AlertManager
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Return if the Alerts should be dismissible by default
     *
     * @return mixed
     */
    public function getDismiss()
    {
        return $this->dismiss;
    }

    /**
     * Set if the Alerts should be dismissible by default
     *
     * @param mixed $dismiss
     * @return AlertManager
     */
    public function setDismiss($dismiss)
    {
        $this->dismiss = $dismiss;

        return $this;
    }
}