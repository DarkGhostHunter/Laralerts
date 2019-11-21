<?php

namespace DarkGhostHunter\Laralerts\Concerns\Alert;

trait HasGettersAndSetters
{
    /**
     * Return the Message for this Alert
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Return if the Alert should be dismissible
     *
     * @return bool
     */
    public function getDismiss()
    {
        return $this->dismiss;
    }

    /**
     * Set if the Alert should be dismissible
     *
     * @param bool $dismiss
     * @return $this
     */
    public function setDismiss(bool $dismiss)
    {
        $this->dismiss = $dismiss;

        return $this;
    }

    /**
     * Return the classes to use in the Alert HTML code
     *
     * @return string
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Set the classes to use in the Alert HTML code
     *
     * @param string $classes
     * @return $this
     */
    public function setClasses(string $classes)
    {
        $this->classes = $classes;

        return $this;
    }
}