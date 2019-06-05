<?php

namespace DarkGhostHunter\Laralerts;

use BadMethodCallException;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;

class AlertFactory implements Htmlable
{
    use Concerns\HasDefaults;

    /**
     * Alert Bag
     *
     * @var \DarkGhostHunter\Laralerts\AlertBag
     */
    protected $alertBag;

    /**
     * View Factory
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $viewFactory;

    /**
     * AlertFactory constructor.
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @param \Illuminate\Contracts\View\Factory $viewFactory
     * @param string $defaultType
     */
    public function __construct(AlertBag $alertBag, ViewFactory $viewFactory, string $defaultType)
    {
        $this->alertBag = $alertBag;
        $this->defaultType = $defaultType;
        $this->viewFactory = $viewFactory;
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
     * Adds a new Alert into the Alert Bag
     *
     * @param \DarkGhostHunter\Laralerts\Alert|null $alert
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function add(Alert $alert)
    {
        $this->alertBag->add($alert);

        return $alert;
    }

    /**
     * Creates a new Alert
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function make()
    {
        $alert = new Alert($this->viewFactory);

        if ($this->defaultDismiss) {
            $alert->dismiss();
        }

        if ($this->defaultType) {
            $alert->setType($this->defaultType);
        }

        return $alert;
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->alertBag->toHtml();
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
            return $this->add(
                $this->make()
            )->{$name}(...$arguments);
        }

        throw new BadMethodCallException("Method $name does not exist.");
    }
}