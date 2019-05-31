<?php

namespace DarkGhostHunter\Laralerts\Concerns;

trait HasDismissible
{
    /**
     * Return if the Alert should be dismissible
     *
     * @return bool
     */
    public function isDismissible()
    {
        return $this->dismissible;
    }
    /**
     * Should the Alert be dismissible
     *
     * @param bool $show
     * @param string $animationClass
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function dismissible(?bool $show = true, ?string $animationClass = 'fade')
    {
        $this->dismissible = true;

        $this->show = $show;

        $this->animationClass = $animationClass;

        return $this;
    }

    /**
     * Should the Alert be fixed (not dismissible)
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function fixed()
    {
        $this->dismissible = false;

        return $this;
    }

    /**
     * Return if the Alert should be showed when is dismissible
     *
     * @return mixed
     */
    public function isShow()
    {
        return $this->show;
    }

    /**
     * Set if the Alert should be showed when is dismissible
     *
     * @param bool $show
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function setShow(bool $show)
    {
        $this->show = $show;

        return $this;
    }

    /**
     * Return the Animation Class used by the Alert for dismissing
     *
     * @return string
     */
    public function getAnimationClass()
    {
        return $this->animationClass;
    }

    /**
     * Set the Animation Class used by the Alert for dismissing
     *
     * @param string $class
     * @return $this
     */
    public function setAnimationClass(string $class)
    {
        $this->animationClass = $class;

        return $this;
    }
}