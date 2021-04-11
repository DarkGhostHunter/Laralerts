<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class Alert implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * The internal key in the Array Bag.
     *
     * @var int
     */
    protected int $key;

    /**
     * Internal Key of the Alert in the Bag so it can be persisted.
     *
     * @var string|null
     */
    protected ?string $persistentKey = null;

    /**
     * The message for the Alert.
     *
     * @var string
     */
    protected string $message = '';

    /**
     * Types for this alert.
     *
     * @var array|string[]
     */
    protected array $types = [];

    /**
     * If this Alert should be able to be dismissible in the frontend.
     *
     * @var bool
     */
    protected bool $dismissible = false;

    /**
     * Returns the ID of the Alert in the Bag.
     *
     * @return string|null
     */
    public function getPersistKey(): ?string
    {
        return $this->persistentKey;
    }

    /**
     * Checks if the current Alert sh
     *
     * @return bool
     */
    public function isPersistent(): bool
    {
        return null !== $this->persistentKey;
    }

    /**
     * Returns the message of the Alert.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Returns the types set for this Alert.
     *
     * @return array|string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Check if the Alert should be dismissible.
     *
     * @return bool
     */
    public function isDismissible(): bool
    {
        return $this->dismissible;
    }

    /**
     * Sets an safely-escaped message.
     *
     * @param  string  $message
     *
     * @return $this
     */
    public function message(string $message): Alert
    {
        return $this->raw(e($message));
    }

    /**
     * Sets a raw (verbatim) message.
     *
     * @param  string  $message
     *
     * @return $this
     */
    public function raw(string $message): Alert
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set a localized message into the Alert.
     *
     * @param  string  $lang
     * @param  array  $replace
     * @param  null  $locale
     *
     * @return $this
     */
    public function trans(string $lang, $replace = [], $locale = null): Alert
    {
        return $this->raw(trans($lang, $replace, $locale));
    }

    /**
     * Sets one or many types for this alert.
     *
     * @param  string  ...$types
     *
     * @return $this
     */
    public function types(string ...$types): Alert
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Sets the Alert as dismissible.
     *
     * @param  bool  $dismissible
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function dismiss(bool $dismissible = true): Alert
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    /**
     * Persists the key into the session, forever.
     *
     * @param  string  $key
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function persistAs(string $key): Alert
    {
        $this->persistentKey = $key;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'types' => $this->types,
            'dismissible' => $this->dismissible,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR);
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Creates a new Alert from a Bag and an array.
     *
     * @param  array  $alert
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public static function fromArray(array $alert): Alert
    {
        $instance = (new static())->raw($alert['message'])->types(...$alert['types'] ?? []);

        if (isset($alert['dismissible'])) {
            $instance->dismiss($alert['dismissible']);
        }

        if (isset($alert['persistent'])) {
            $instance->persistAs($alert['persistent']);
        }

        return $instance;
    }
}
