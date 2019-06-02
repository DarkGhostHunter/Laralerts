<?php

namespace DarkGhostHunter\Laralerts;

use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class AlertBag implements Arrayable, Countable, Jsonable, JsonSerializable, Htmlable
{
    /**
     * Alerts inside the Session
     *
     * @var array
     */
    protected $alerts = [];

    /**
     * Return the Alerts array
     *
     * @return array
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Set the Alerts array
     *
     * @param array $alerts
     * @return void
     */
    public function setAlerts(array $alerts)
    {
        $this->alerts = $alerts;
    }

    /**
     * Adds an Alert
     *
     * @param \DarkGhostHunter\Laralerts\Alert $alert
     * @return void
     */
    public function add(Alert $alert)
    {
        $this->alerts[] = $alert;
    }

    /**
     * Alias for the add method
     *
     * @param \DarkGhostHunter\Laralerts\Alert $alert
     * @return void
     */
    public function append(Alert $alert)
    {
        $this->add($alert);
    }

    /**
     * Prepends an Alert to the bag
     *
     * @param \DarkGhostHunter\Laralerts\Alert $alert
     * @return void
     */
    public function prepend(Alert $alert)
    {
        array_unshift($this->alerts, $alert);
    }

    /**
     * Removes the first Alert of the Bag
     *
     * @return void
     */
    public function removeFirst()
    {
        array_shift($this->alerts);
    }

    /**
     * Removes the last Alert of the bag
     *
     * @return void
     */
    public function removeLast()
    {
        array_pop($this->alerts);
    }

    /**
     * Return if the AlertBag has Alerts
     *
     * @return bool
     */
    public function hasAlerts()
    {
        return !empty($this->alerts);
    }

    /**
     * Removes all Alerts of the bag
     *
     * @return void
     */
    public function flush()
    {
        $this->alerts = [];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $alerts = [];

        foreach ($this->alerts as $alert) {
            $alerts[] = $alert->toArray();
        }

        return $alerts;
    }

    /**
     * Count elements of an object
     *
     * @return int
     */
    public function count()
    {
        return count($this->alerts);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        $tag = '';

        foreach ($this->alerts as $alert) {
            /** @var \DarkGhostHunter\Laralerts\Alert $alert */
            $tag .= $alert->toHtml();
        }

        return $tag;
    }

    /**
     * Return a string representation of this instance
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}