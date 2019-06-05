<?php

namespace DarkGhostHunter\Laralerts\Concerns;

trait HasClasses
{
    /**
     * Type of Alert (class)
     *
     * @var string
     */
    protected $type;

    /**
     * Additional classes to add to the HTML tag
     *
     * @var string
     */
    protected $classes;

    /**
     * Sets the Alert class
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function primary()
    {
        return $this->setType(__FUNCTION__);
    }

    /**
     * Sets the Alert class
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function secondary()
    {
        return $this->setType(__FUNCTION__);
    }

    /**
     * Sets the Alert class
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function success()
    {
        return $this->setType(__FUNCTION__);
    }

    /**
     * Sets the Alert class
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function danger()
    {
        return $this->setType(__FUNCTION__);
    }

    /**
     * Sets the Alert class
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function warning()
    {
        return $this->setType(__FUNCTION__);
    }

    /**
     * Sets the Alert class
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function info()
    {
        return $this->setType(__FUNCTION__);
    }

    /**
     * Sets the Alert class
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function light()
    {
        return $this->setType(__FUNCTION__);
    }

    /**
     * Sets the Alert class
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function dark()
    {
        return $this->setType(__FUNCTION__);
    }

    /**
     * Return the Alert type class
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the Alert type class
     *
     * @param string $type
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function setType(string $type)
    {
        if (!in_array($type, self::TYPES, false)) {
            throw new \BadMethodCallException("The [$type] is not a valid Alert type");
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Return the custom classes as a string
     *
     * @return string
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Set custom classes for the Alert
     *
     * @param mixed ...$classes
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function classes(...$classes)
    {
        if (is_array($classes[0]) && func_num_args() === 1) {
            $classes = $classes[0];
        }

        $this->classes = trim(implode(' ', $classes));

        return $this;
    }
}