<?php

namespace DarkGhostHunter\Laralerts;

use ArrayIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

class AlertBag implements Arrayable, Countable, IteratorAggregate, Serializable, JsonSerializable, Jsonable
{
    /**
     * Alerts active in the application lifecycle
     *
     * @var array
     */
    protected $alerts = [];

    /**
     * Return all the active Alerts
     *
     * @return array
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Set all the active Alerts
     *
     * @param array $alerts
     */
    public function setAlerts(array $alerts)
    {
        $this->alerts = $alerts;
    }

    /**
     * If the Alert Bag has Alerts
     *
     * @return bool
     */
    public function hasAlerts()
    {
        return $this->alerts !== [];
    }

    /**
     * If the Alert Bag doesn't have Alerts
     *
     * @return bool
     */
    public function doesntHaveAlerts()
    {
        return ! $this->hasAlerts();
    }

    /**
     * Adds an Alert
     *
     * @param \DarkGhostHunter\Laralerts\Alert $alert
     */
    public function add(Alert $alert)
    {
        $this->alerts[] = $alert;
    }

    /**
     * Remove the first Alert
     *
     * @return $this
     */
    public function removeFirst()
    {
        array_shift($this->alerts);

        return $this;
    }

    /**
     * Remove the last Alert
     *
     * @return $this
     */
    public function removeLast()
    {
        array_pop($this->alerts);

        return $this;
    }

    /**
     * Flushes all Alerts to the toilet
     *
     * @return $this
     */
    public function flush()
    {
        $this->alerts = [];

        return $this;
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
     * Retrieve an external iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->alerts);
    }

    /**
     * String representation of object
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->alerts);
    }

    /**
     * Constructs the object
     *
     * @param string
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->alerts = unserialize($serialized, [__CLASS__, Alert::class]);
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
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}