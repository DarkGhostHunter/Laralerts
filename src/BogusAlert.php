<?php

namespace DarkGhostHunter\Laralerts;

use Countable;

/**
 * @internal  This is only a bogus alert that does nothing, and it's not added to the Alert bag.
 * @codeCoverageIgnore
 */
class BogusAlert extends Alert
{
    /**
     * Sets a safely-escaped message.
     *
     * @param  string  $message
     * @return $this
     */
    public function message(string $message): static
    {
        return $this;
    }

    /**
     * Sets a raw, non-escaped, message.
     *
     * @param  string  $message
     * @return $this
     */
    public function raw(string $message): static
    {
        return $this;
    }

    /**
     * Set a localized message into the Alert.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return $this
     */
    public function trans(string $key, array $replace = [], string $locale = null): static
    {
        return $this;
    }

    /**
     * Sets a localized pluralized message into the Alert.
     *
     * @param  string  $key
     * @param  \Countable|int|array  $number
     * @param  array  $replace
     * @param  string|null  $locale
     * @return $this
     */
    public function transChoice(
        string $key,
        Countable|int|array $number,
        array $replace = [],
        string $locale = null
    ): static
    {
        return $this;
    }

    /**
     * Sets one or many types for this alert.
     *
     * @param  string  ...$types
     * @return $this
     */
    public function types(string ...$types): static
    {
        return $this;
    }

    /**
     * Sets the Alert as dismissible.
     *
     * @param  bool  $dismissible
     * @return $this
     */
    public function dismiss(bool $dismissible = true): static
    {
        return $this;
    }

    /**
     * Persists the key into the session, forever.
     *
     * @param  string  $key
     * @return $this
     */
    public function persistAs(string $key): static
    {
        return $this;
    }

    /**
     * Adds an external link that should be replaced before rendering the Alert.
     *
     * @param  string  $replace
     * @param  string  $url
     * @param  bool  $blank
     * @return $this
     */
    public function away(string $replace, string $url, bool $blank = true): static
    {
        return $this;
    }

    /**
     * Adds a link that should be replaced before rendering the Alert.
     *
     * @param  string  $replace
     * @param  string  $url
     * @param  bool  $blank
     * @return $this
     */
    public function to(string $replace, string $url, bool $blank = false): static
    {
        return $this;
    }

    /**
     * Adds a link to a route that should be replaced before rendering the Alert.
     *
     * @param  string  $replace
     * @param  string  $name
     * @param  array  $parameters
     * @param  bool  $blank
     * @return $this
     */
    public function route(string $replace, string $name, array $parameters = [], bool $blank = false): static
    {
        return $this;
    }

    /**
     * Adds a link to an action that should be replaced before rendering the Alert.
     *
     * @param  string  $replace
     * @param  string|array  $action
     * @param  array  $parameters
     * @param  bool  $blank
     * @return $this
     */
    public function action(string $replace, string|array $action, array $parameters = [], bool $blank = false): static
    {
        return $this;
    }

    /**
     * Tags the alert.
     *
     * @param  string  ...$tags
     * @return $this
     */
    public function tag(string ...$tags): static
    {
        return $this;
    }

}
