<?php

namespace DarkGhostHunter\Laralerts;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

use function array_key_last;
use function json_decode;
use function value;

use const JSON_THROW_ON_ERROR;

/**
 * @mixin \DarkGhostHunter\Laralerts\Alert
 */
class Bag
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The underlying collection of alerts.
     *
     * @var \Illuminate\Support\Collection
     */
    protected Collection $alerts;

    /**
     * A key-value pair that indicates which alerts must persist.
     *
     * @var array
     */
    protected array $persisted = [];

    /**
     * Create a new Bag instance.
     *
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  \Illuminate\Contracts\Config\Repository  $config
     */
    public function __construct(protected Session $session, protected Repository $config)
    {
        $this->alerts = new Collection;
    }

    /**
     * Returns all a key-index map of all persisted alerts.
     *
     * @return array
     */
    public function getPersisted(): array
    {
        return $this->persisted;
    }

    /**
     * Creates a new Alert into this Bag instance.
     *
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function new(): Alert
    {
        $this->add($alert = new Alert($this));

        return $alert;
    }

    /**
     * Adds an Alert into the bag.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert|\DarkGhostHunter\Laralerts\Alert[]  $alert
     * @return \DarkGhostHunter\Laralerts\Bag
     */
    public function add(Alert|array $alert): static
    {
        foreach (Arr::wrap($alert) as $item) {
            $this->alerts->push($item);

            $item->index = array_key_last($this->alerts->all());

            // The method is also used to put alerts from the session. Because
            // of that, we will check if it already has a persistent key and,
            // if it has one, we will add it to the internal map of alerts.
            if ($key = $item->getPersistKey()) {
                $this->persisted[$key] = $item->index;
            }

            $item->setBag($this);
        }

        return $this;
    }

    /**
     * Returns the underlying collection of alerts.
     *
     * @return \Illuminate\Support\Collection|\DarkGhostHunter\Laralerts\Alert[]
     */
    public function collect(): Collection
    {
        return $this->alerts;
    }

    /**
     * Marks an existing Alert as persistent.
     *
     * @param  string  $key
     * @param  int  $index
     * @return $this
     */
    public function markPersisted(string $key, int $index): static
    {
        // Find if there is a key already for the persisted alert and replace it.
        $this->abandon($key);

        $this->persisted[$key] = $index;

        return $this;
    }

    /**
     * Abandons a persisted Alert.
     *
     * @param  string  $key
     * @return bool  Returns true if successful.
     */
    public function abandon(string $key): bool
    {
        if (null !== $index = $this->whichPersistent($key)) {
            $this->alerts->forget($index);
            unset($this->persisted[$key]);
            return true;
        }

        return false;
    }

    /**
     * Check if an Alert by the given key is persistent.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasPersistent(string $key): bool
    {
        return null !== $this->whichPersistent($key);
    }

    /**
     * Locates the key of a persistent alert.
     *
     * @param  string  $key
     * @return int|null
     */
    protected function whichPersistent(string $key): ?int
    {
        return $this->persisted[$key] ?? null;
    }

    /**
     * Deletes all alerts.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->alerts = new Collection();
    }

    /**
     * Creates an Alert only if the condition evaluates to true.
     *
     * @param  \Closure|bool  $condition
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function when(Closure|bool $condition): Alert
    {
        return value($condition, $this) ? $this->new() : new BogusAlert($this);
    }

    /**
     * Creates an Alert only if the condition evaluates to false.
     *
     * @param  \Closure|bool  $condition
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function unless(Closure|bool $condition): Alert
    {
        return ! value($condition, $this) ? $this->new() : new BogusAlert($this);
    }

    /**
     * Adds an Alert into the bag from a JSON string.
     *
     * @param  string  $alert
     * @param  int  $options
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \JsonException
     */
    public function fromJson(string $alert, int $options = 0): Alert
    {
        $this->add($instance = Alert::fromArray($this, json_decode($alert, true, 512, $options | JSON_THROW_ON_ERROR)));

        return $instance;
    }

    /**
     * Pass through all calls to a new Alert.
     *
     * @codeCoverageIgnore
     * @param  string  $method
     * @param  array  $parameters
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function __call(string $method, array $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->new()->{$method}(...$parameters);
    }
}
