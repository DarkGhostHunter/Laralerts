<?php

namespace DarkGhostHunter\Laralerts\Testing;

use Countable;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Testing\Fakes\BagFake;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert as PHPUnit;
use function action;
use function in_array;
use function is_array;
use function is_string;
use function route;
use function sort;
use function strcmp;
use function trans;
use function trans_choice;
use function url;
use function usort;

class Builder
{
    /**
     * Create a new expectation builder instance.
     *
     * @param  \DarkGhostHunter\Laralerts\Testing\Fakes\BagFake  $bag
     * @param  string|null  $message
     * @param  string[]|null  $types
     * @param  bool|null  $dismiss
     * @param  string[]|bool|null  $persisted
     * @param  string[]|null  $tags
     * @param  string[]|null  $links
     * @param  bool  $anyTag
     */
    public function __construct(
        public BagFake $bag,
        protected ?string $message = null,
        protected ?array $types = null,
        protected ?bool $dismiss = null,
        protected array|bool|null $persisted = null,
        protected ?array $tags = null,
        protected ?array $links = null,
        protected bool $anyTag = false,
    )
    {
        //
    }

    /**
     * Expect an alert with the raw message.
     *
     * @param  string  $message
     * @return $this
     */
    public function withRaw(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Expect an alert with the message.
     *
     * @param  string  $message
     * @return $this
     */
    public function withMessage(string $message): static
    {
        return $this->withRaw(e($message));
    }

    /**
     * Expect an alert with a translated message.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return $this
     */
    public function withTrans(string $key, array $replace = [], string $locale = null): static
    {
        return $this->withRaw(trans($key, $replace, $locale));
    }

    /**
     * Expect an alert with a translated (choice) message.
     *
     * @param  string  $key
     * @param  Countable|int|array  $number
     * @param  array  $replace
     * @param  string|null  $locale
     * @return $this
     */
    public function withTransChoice(
        string $key,
        Countable|int|array $number,
        array $replace = [],
        string $locale = null
    ): static
    {
        return $this->withRaw(trans_choice($key, $number, $replace, $locale));
    }

    /**
     * Expect an alert with a link away.
     *
     * @param  string  $replace
     * @param  string  $url
     * @param  bool  $blank
     * @return $this
     */
    public function withAway(string $replace, string $url, bool $blank = true): static
    {
        $this->links[] = (object) [
            'replace' => $replace,
            'url'     => $url,
            'blank'   => $blank,
        ];

        usort($this->links, static function (object $first, object $second): int {
            return strcmp($first->replace . $first->url, $second->replace . $second->url);
        });

        return $this;
    }

    /**
     * Expect an alert with a link to a path.
     *
     * @param  string  $replace
     * @param  string  $url
     * @param  bool  $blank
     * @return $this
     */
    public function withTo(string $replace, string $url, bool $blank = false): static
    {
        return $this->withAway($replace, url($url), $blank);
    }

    /**
     * Expect an alert with a link to a route.
     *
     * @param  string  $replace
     * @param  string  $name
     * @param  array  $parameters
     * @param  bool  $blank
     * @return $this
     */
    public function withRoute(string $replace, string $name, array $parameters = [], bool $blank = false): static
    {
        return $this->withAway($replace, route($name, $parameters), $blank);
    }

    /**
     * Expect an alert with a link to an action.
     *
     * @param  string  $replace
     * @param  string|array  $action
     * @param  array  $parameters
     * @param  bool  $blank
     * @return $this
     */
    public function withAction(string $replace, string|array $action, array $parameters = [], bool $blank = false): static
    {
        return $this->withAway($replace, action($action), $blank);
    }

    /**
     * Expect an alert with the issued types.
     *
     * @param  string  ...$types
     * @return $this
     */
    public function withTypes(string ...$types): static
    {
        $this->types = $types;

        sort($this->types);

        return $this;
    }

    /**
     * Expect an alert persisted.
     *
     * @return $this
     */
    public function persisted(): static
    {
        $this->persisted = true;

        return $this;
    }

    /**
     * Expect an alert not persisted.
     *
     * @return $this
     */
    public function notPersisted(): static
    {
        $this->persisted = false;

        return $this;
    }

    /**
     * Expect an alert dismissible.
     *
     * @return $this
     */
    public function dismissible(): static
    {
        $this->dismiss = true;

        return $this;
    }

    /**
     * Expect an alert not dismissible.
     *
     * @return $this
     */
    public function notDismissible(): static
    {
        $this->dismiss = false;

        return $this;
    }

    /**
     * Expect an alert with the given tags.
     *
     * @param  string  ...$tags
     * @return $this
     */
    public function withTag(string ...$tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Expect an alert with any of the given tags.
     *
     * @param  string  ...$tags
     * @return $this
     */
    public function withAnyTag(string ...$tags): static
    {
        $this->anyTag = true;

        return $this->withTag(...$tags);
    }
    /**
     * Returns a collection of all matching alerts.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function matches(): Collection
    {
        return $this->bag->added->filter(function (Alert $alert): bool {
            return $this->is($alert);
        });
    }

    /**
     * Check if the given alert matches the expectations.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     * @return bool
     */
    protected function is(Alert $alert): bool
    {
        if ($this->message !== null && $this->message !== $alert->getMessage())  {
            return false;
        }

        if ($this->dismiss !== null && $this->dismiss !== $alert->isDismissible()) {
            return false;
        }

        if ($this->types !== null && $this->types !== $alert->getTypes()) {
            return false;
        }

        if ($this->tags !== null) {
            if ($this->anyTag) {
                return $alert->hasAnyTag(...$this->tags);
            }

            return $this->tags === $alert->getTags();
        }

        if ($this->persisted !== null) {
            if (is_string($this->persisted)) {
                return $this->persisted === $alert->getPersistKey();
            }

            if (is_array($this->persisted)) {
                return in_array($alert->getPersistKey(), $this->persisted, true);
            }

            return $this->persisted === (bool) $alert->getPersistKey();
        }

        if ($this->links !== null && $this->links != $alert->getLinks()) {
            return false;
        }

        return true;
    }

    /**
     * Expect an alert persisted with the issued key.
     *
     * @param  string|array  $key
     * @return void
     */
    public function persistedAs(string ...$key): void
    {
        $this->persisted = $key;

        $count = count($key);

        $this->count($count, "Failed to assert that [$count] persistent alerts exist.");
    }

    /**
     * Assert that at least one Alert exists with the given expectations.
     *
     * @param  string  $message
     * @return void
     */
    public function exists(string $message = 'Failed to assert that at least one alert matches the expectations.'): void
    {
        PHPUnit::assertNotEmpty($this->matches(), $message);
    }

    /**
     * Assert that no Alert exists with the given expectations.
     *
     * @param  string  $message
     * @return void
     */
    public function missing(string $message = 'Failed to assert that no alert matches the expectations.'): void
    {
        PHPUnit::assertEmpty($this->matches(), $message);
    }

    /**
     * Assert that only one Alert exists with the given expectations.
     *
     * @param  string  $message
     * @return void
     */
    public function unique(string $message = 'Failed to assert that there is only one alert.'): void
    {
        $this->count(1, $message);
    }

    /**
     * Assert that the given number of Alerts matches exactly the given expectations.
     *
     * @param  int  $count
     * @param  string|null  $message
     * @return void
     */
    public function count(int $count, string $message = null): void
    {
        $matches = $this->matches();

        PHPUnit::assertCount(
            $count, $matches,
            $message ?? "Failed to assert that [{$matches->count()}] alerts match the expected [$count] count."
        );
    }
}
