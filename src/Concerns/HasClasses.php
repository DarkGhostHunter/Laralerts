<?php

namespace DarkGhostHunter\Laralerts\Concerns;

trait HasClasses
{

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
     * @param string $class
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function setType(string $class)
    {
        if (!in_array($class, self::TYPES, true)) {
            throw new \BadMethodCallException("The $class is not a valid Alert type");
        }

        $this->type = $class;

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

        $this->classes = implode(' ', $classes);

        return $this;
    }
}