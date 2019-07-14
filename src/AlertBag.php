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
     * If the Alert Bag should be kept for another Request
     *
     * @var bool
     */
    protected $reflash = false;

    /**
     * If the Alert Bag has been modified;
     *
     * @var bool
     */
    protected $dirty = false;

    /**
     * Alerts active in the application lifecycle
     *
     * @var array
     */
    protected $alerts = [];

    /**
     * Sets if the Alert Bag has been modified
     *
     * @param bool $dirty
     * @return AlertBag
     */
    public function setDirty(bool $dirty)
    {
        $this->dirty = $dirty;

        return $this;
    }

    /**
     * Returns if the Alert Bag has been modified
     *
     * @return bool
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     * Sets if the Alert Bag should be kept for another Request
     *
     * @return \DarkGhostHunter\Laralerts\AlertBag
     */
    public function markForReflash()
    {
        $this->reflash = true;

        return $this;
    }

    /**
     * Returns if the Alert Bag should be kept for another Request
     *
     * @return bool
     */
    public function shouldReflash()
    {
        return $this->reflash;
    }

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
     * @return \DarkGhostHunter\Laralerts\AlertBag
     */
    public function setAlerts(array $alerts)
    {
        $this->alerts = $alerts;

        $this->dirty = true;

        return $this;
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
     * @return \DarkGhostHunter\Laralerts\AlertBag
     */
    public function add(Alert $alert)
    {
        $this->alerts[] = $alert;

        $this->dirty = true;

        return $this;
    }

    /**
     * Remove the first Alert
     *
     * @return $this
     */
    public function removeFirst()
    {
        array_shift($this->alerts);

        $this->dirty = true;

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

        $this->dirty = true;

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

        $this->dirty = true;

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
        return serialize([
            'alerts' => $this->alerts,
            'keep' => $this->reflash
        ]);
    }

    /**
     * Constructs the object
     *
     * @param string
     * @return void
     */
    public function unserialize($serialized)
    {
        ['alerts' => $this->alerts, 'keep' => $this->reflash] = unserialize($serialized, [__CLASS__, Alert::class]);
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