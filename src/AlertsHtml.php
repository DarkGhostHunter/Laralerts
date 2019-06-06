<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\View\Factory as ViewFactory;

class AlertsHtml implements Htmlable
{
    /**
     * Alerts Bag
     *
     * @var \DarkGhostHunter\Laralerts\AlertBag
     */
    protected $alertBag;

    /**
     * View Factory
     *
     * @var \Illuminate\View\Factory
     */
    protected $viewFactory;

    /**
     * AlertRenderer constructor.
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alerts
     * @param \Illuminate\View\Factory $viewFactory
     */
    public function __construct(AlertBag $alerts, ViewFactory $viewFactory)
    {
        $this->alertBag = $alerts;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->alertBag->doesntHaveAlerts()) {
            return '';
        }

        return new HtmlString(
            $this->viewFactory->make('laralerts::alerts', [
                'alerts' => $this->alertsToHtml()
            ])->render()
        );
    }

    /**
     * Return the Array of alerts
     *
     * @return array
     */
    protected function alertsToHtml()
    {
        $alerts = [];

        foreach ($this->alertBag as $alert) {
            $alerts[] = new HtmlString(
                $this->viewFactory->make(
                    $alert->getDismiss() ? 'laralerts::alert-dismiss' : 'laralerts::alert', $alert->toArray()
                )->render()
            );
        }

        return $alerts;
    }

    /**
     * The string representation of this class
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->toHtml();
    }
}