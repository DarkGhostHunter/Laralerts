<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class Alert implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * Internal Key of the Alert in the Bag so it can be persisted.
     *
     * @var string
     */
    protected string $key;

    /**
     * The message for the Alert.
     *
     * @var string
     */
    protected string $message;

    /**
     * Types for this alert.
     *
     * @var array|string[]
     */
    protected array $types;

    /**
     * If this Alert should be able to be dismissible in the frontend.
     *
     * @var bool
     */
    protected bool $dismissible;

    /**
     * Links that should be replaced in the alert message.
     *
     * @var array
     */
    protected array $links = [];

    /**
     * Check if the alert should persist in the session, if possible.
     *
     * @var string|null
     */
    protected ?string $persistentKey = null;

    /**
     * Check if this Alert should be conditionally rendered.
     *
     * @var bool
     */
    protected bool $render = true;

    /**
     * Alerts bag.
     *
     * @var \DarkGhostHunter\Laralerts\Bag|null
     */
    protected ?Bag $bag = null;

    /**
     * Alert constructor.
     *
     * @param  \DarkGhostHunter\Laralerts\Bag  $bag
     */
    public function __construct(Bag $bag)
    {
        $this->bag = $bag;
    }

    /**
     * Returns the ID of the Alert in the Bag.
     *
     * @return string
     */
    public function getPersistKey(): string
    {
        return $this->key;
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
     * Returns the links to be replaced in the message.
     *
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
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
     * Check if the Alert should be rendered.
     *
     * @return bool
     */
    public function isRender(): bool
    {
        return $this->render;
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
     * Sets links and keys to replaced from the message.
     *
     * @param  string|array|string[]  $name
     * @param  string|null  $value
     *
     * @return $this
     */
    public function links($name, string $value = null): Alert
    {
        $this->links = is_array($name) ? $name : [$name => $value];

        return $this;
    }

    /**
     * Sets the Alert to be rendered when the condition is truthy.
     *
     * @param  mixed  $condition
     */
    public function when($condition): Alert
    {
        $this->render = (bool)value($condition);

        return $this;
    }

    /**
     * Sets the Alert to be rendered unless the condition is false.
     *
     * @param $condition
     *
     * @return $this
     */
    public function unless($condition): Alert
    {
        $this->render = !(bool)value($condition);

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
            'persistent' => $this->persistentKey
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     *
     * @return string
     * @throws \JsonException
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
     * @param  \DarkGhostHunter\Laralerts\Bag  $bag
     * @param  array  $alert
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public static function fromArray(Bag $bag, array $alert): Alert
    {
        $instance = (new static())->raw($alert['message'])->types($alert['types'] ?? []);

        if (isset($alert['dismissible'])) {
            $instance->dismiss($alert['dismissible']);
        }

        $instance->setBag($bag);

        if (isset($alert['persistent'])) {
            $instance->persistAs($alert['persistent']);
        }

        return $instance;
    }
}
