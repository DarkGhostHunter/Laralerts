<?php

namespace DarkGhostHunter\Laralerts;

use Countable;
use Serializable;
use ArrayIterator;
use JsonSerializable;
use IteratorAggregate;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class AlertBag implements Arrayable, Countable, IteratorAggregate, Serializable, JsonSerializable, Jsonable
{
    /**
     * If the Alert Bag has been modified;
     *
     * @var bool
     */
    protected $dirty = false;

    /**
     * Alerts from the past request
     *
     * @var array
     */
    protected $old = [];

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
     * Rescue the Alerts sent from the previous request into the Alert Bag
     *
     * @return \DarkGhostHunter\Laralerts\AlertBag
     */
    public function reflash()
    {
        $this->alerts = array_merge($this->old, $this->alerts);

        $this->old = [];

        return $this;
    }

    /**
     * Take the current alerts (sent) and set them as old
     *
     * @return $this
     */
    public function ageAlerts()
    {
        $this->old = $this->alerts;

        $this->alerts = [];

        return $this;
    }

    /**
     * Return the old alerts, if any
     *
     * @return array
     */
    public function getOld()
    {
        return $this->old;
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
     * Filter Alerts by message or type
     *
     * @param  string $message
     * @param  string|null $type
     * @return array
     */
    public function filterByMessage(string $message, string $type = null)
    {
        return collect($this->alerts)
            ->filter(function($alert) use ($message) {
                return $alert->getMessage() === $message;
            })
            ->when($type, function ($alerts, $value) {
                return $alerts->filter(function($alert) use ($value) {
                    return $alert->getType() === $value;
                });
            })->values()->all();
    }

    /**
     * Filter Alerts by type
     *
     * @param  string $type
     * @return array
     */
    public function filterByType(string $type)
    {
        return collect($this->alerts)->filter(function($alert) use ($type) {
            return $alert->getType() === $type;
        })->values()->all();
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
            'old' => $this->old,
            'alerts' => $this->alerts,
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
        [
            'alerts' => $this->alerts,
        ] = unserialize($serialized, [__CLASS__, Alert::class]);
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
