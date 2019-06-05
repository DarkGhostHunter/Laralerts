<?php


namespace DarkGhostHunter\Laralerts\Concerns;


use BadMethodCallException;
use DarkGhostHunter\Laralerts\Alert;

trait HasDefaults
{
    /**
     * Default Type for the Alerts
     *
     * @var string
     */
    protected $defaultType;

    /**
     * Default Dismissible property for the Alerts
     *
     * @var bool
     */
    protected $defaultDismiss = false;

    /**
     * Return the Default Type of the Alerts
     *
     * @return string
     */
    public function getDefaultType()
    {
        return $this->defaultType;
    }

    /**
     * Set the Default Type of the Alerts
     *
     * @param string $defaultType
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function setDefaultType(string $defaultType)
    {
        if (!in_array($defaultType, Alert::TYPES, true)) {
            throw new BadMethodCallException("The $defaultType is not a valid Alert type.");
        }

        $this->defaultType = $defaultType;

        return $this;
    }

    /**
     * Return the Default dismissible property for Alerts
     *
     * @return bool
     */
    public function isDefaultDismiss()
    {
        return $this->defaultDismiss;
    }

    /**
     * Should the Alert be dismissible
     *
     * @param bool $dismissible
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function setDefaultDismiss(bool $dismissible)
    {
        $this->defaultDismiss = $dismissible;

        return $this;
    }
}