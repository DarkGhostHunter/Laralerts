<?php

namespace DarkGhostHunter\Laralerts;

use BadMethodCallException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Serializable;

class Alert implements Arrayable, Serializable, Jsonable, JsonSerializable, Htmlable
{
    use Concerns\Alert\HasTypes,
        Concerns\Alert\HasGettersAndSetters;

    /**
     * Alert message
     *
     * @var string
     */
    protected $message;

    /**
     * If the Alert should be dismissible
     *
     * @var bool
     */
    protected $dismiss;

    /**
     * Classes to add into the Alert HTML string
     *
     * @var string
     */
    protected $classes;

    /**
     * Create a new Alert instance
     *
     * @param string $message
     * @param string $type
     * @param bool $dismiss
     * @param string $classes
     */
    public function __construct(string $message = null,
                                string $type = null,
                                bool $dismiss = null,
                                string $classes = null)
    {
        $this->message = $message;
        $this->type = $type;
        $this->dismiss = $dismiss;
        $this->classes = $classes;
    }
    /**
     * Set the message for this Alert
     *
     * @param string $message
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function message(string $message)
    {
        return $this->raw(e($message));
    }

    /**
     * Set a raw string into the Alert
     *
     * @param string $message
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function raw(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set a localized message into the Alert
     *
     * @param string $lang
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function lang(string $lang)
    {
        return $this->raw(__($lang));
    }

    /**
     * Set the Alert as dismissible
     *
     * @return $this
     */
    public function dismiss()
    {
        $this->dismiss = true;

        return $this;
    }

    /**
     * Set the Alert as not dismissible (fixed)
     *
     * @return $this
     */
    public function fixed()
    {
        $this->dismiss = false;

        return $this;
    }

    /**
     * Set a list of classes to use in the Alert HTML code
     *
     * @param mixed ...$classes
     * @return $this
     */
    public function classes(...$classes)
    {
        if (is_array($classes) && func_num_args() === 1) {
            $classes = $classes[0];
        }

        $this->classes = implode(' ', $classes);

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'dismiss' => $this->dismiss,
            'classes' => $this->classes,
        ];
    }

    /**
     * String representation of object
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Constructs the object
     *
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        [
            'message' => $this->message,
            'type' => $this->type,
            'dismiss' => $this->dismiss,
            'classes' => $this->classes,
        ] = unserialize($serialized, [__CLASS__]);
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
     * Creates a new Alert instance from an array
     *
     * @param array $attributes
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public static function fromArray(array $attributes)
    {
        return new Alert(...[
            $attributes['message'],
            $attributes['type'] ?? null,
            $attributes['dismiss'] ?? null,
            $attributes['classes'] ?? null,
        ]);
    }

    /**
     * Creates a new Alert instance from a JSON string
     *
     * @param string $json
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public static function fromJson(string $json)
    {
        return static::fromArray(json_decode($json, true));
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
        return view($this->dismiss ? 'laralerts::alert-dismiss' : 'laralerts::alert', $this->toArray());
    }

    /**
     * If the call was made to a type, set that type or bail out.
     *
     * @param $method
     * @param $parameters
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, self::$types, false)) {
            return $this->setType($method);
        }

        throw new BadMethodCallException("Method $method does not exist.");
    }
}