<?php

namespace DarkGhostHunter\Laralerts\Concerns;

trait HasDismissible
{
    /**
     * If the Alert should be dismissible
     *
     * @var bool
     */
    protected $dismiss;

    /**
     * Return if the Alert should be dismissible
     *
     * @return bool
     */
    public function isDismiss()
    {
        return $this->dismiss;
    }

    /**
     * Should the Alert be dismissible
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function dismiss()
    {
        $this->dismiss = true;

        return $this;
    }

    /**
     * Should the Alert be fixed (not dismissible)
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function fixed()
    {
        $this->dismiss = false;

        return $this;
    }
}