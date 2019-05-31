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
    protected $defaultDismissible = false;

    /**
     * If the dismissible Alerts should start displayed using the 'show' class
     *
     * @var boolean
     */
    protected $defaultShow = true;

    /**
     * The animation class for dismissal
     *
     * @var string
     */
    protected $defaultAnimationClass;

    /**
     * Default set of classes to inject
     *
     * @var array
     */
    protected $defaultClasses;


    /**
     * Return the default Animation Classes when the Alert is dismissible
     *
     * @return string
     */
    public function getDefaultAnimationClass()
    {
        return $this->defaultAnimationClass;
    }

    /**
     * Set the default Animation Classes when the Alert is dismissible
     *
     * @param string $defaultAnimationClass
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function setDefaultAnimationClass(string $defaultAnimationClass)
    {
        $this->defaultAnimationClass = $defaultAnimationClass;

        return $this;
    }

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
     * Return the Default Classes to set into the Alerts
     *
     * @return array
     */
    public function getDefaultClasses()
    {
        return $this->defaultClasses;
    }

    /**
     * Set the Default Classes to set into the Alerts
     *
     * @param array $defaultClasses
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function setDefaultClasses(...$defaultClasses)
    {
        if (is_array($defaultClasses[0]) && func_num_args() === 1) {
            $defaultClasses = $defaultClasses[0];
        }

        $this->defaultClasses = $defaultClasses;

        return $this;
    }

    /**
     * Return if the Alerts should be showed by default when dismissible
     *
     * @return bool
     */
    public function isDefaultShow()
    {
        return $this->defaultShow;
    }

    /**
     * Sets if Alerts should be showed by default when dismissible
     *
     * @param bool $defaultShow
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function setDefaultShow(bool $defaultShow)
    {
        $this->defaultShow = $defaultShow;

        return $this;
    }

    /**
     * Return the Default dismissible property for Alerts
     *
     * @return bool
     */
    public function isDefaultDismissible()
    {
        return $this->defaultDismissible;
    }

    /**
     * Should the Alert be dismissible
     *
     * @param bool $dismissible
     * @return \DarkGhostHunter\Laralerts\AlertFactory
     */
    public function setDefaultDismissible(bool $dismissible)
    {
        $this->defaultDismissible = $dismissible;

        return $this;
    }
}