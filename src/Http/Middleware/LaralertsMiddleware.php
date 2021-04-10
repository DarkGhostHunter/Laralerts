<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use Illuminate\Contracts\Session\Session;

class LaralertsMiddleware
{
    /**
     * @var \Illuminate\Contracts\Session\Session
     */
    protected Session $session;

    /**
     * Application config.
     *
     * @var string
     */
    protected string $key;
    /**
     * @var \DarkGhostHunter\Laralerts\Bag
     */
    protected Bag $bag;

    /**
     * Laralerts Middleware constructor.
     *
     * @param  \DarkGhostHunter\Laralerts\Bag  $bag
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  string  $key
     */
    public function __construct(Bag $bag, Session $session, string $key)
    {
        $this->session = $session;
        $this->bag = $bag;
        $this->key = $key;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If the session hasn't started, do nothing and bypass this.
        if (! $this->session->isStarted()) {
            return $next($request);
        }

        // Locate any alert marked as persistent and populate the bag with these.
        // We will also remove them from the session itself just for cleaning.
        // Obviously there should be no problem because the session started.
        $this->moveAlertsToBag();

        $response = $next($request);

        // Before handling the response, we will take only those alerts marked
        // as persistent and save them into the session so later these can be
        // retrieved using the bag. Only then the developer can delete them.
        $this->copyAlertsToSession();

        return $response;
    }

    /**
     * Add alerts persisted into the session back to the Bag.
     *
     * @return void
     */
    protected function moveAlertsToBag(): void
    {
        foreach ($this->session->get($this->key) as $alert) {
            Alert::fromArray($this->bag, $alert);
        }

        $this->session->forget($this->key);
    }

    /**
     * Callback to transform an alert into a persistable array.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     *
     * @return array
     */
    protected static function alertToArray(Alert $alert): array
    {
        $array = $alert->toArray();

        unset($array['persistent']);

        return $array;
    }


    /**
     * Move the alerts back to the session.
     *
     * @return void
     */
    protected function copyAlertsToSession(): void
    {
        $this->session->put(
            $this->key,
            array_map([static::class, 'alertToArray'], $this->bag->getPersistentAlerts())
        );
    }

    /**
     * Returns alerts persisted in the session.
     *
     * @return array
     */
    protected function persistedAlerts(): array
    {
        return $this->session->get($this->key, []);
    }
}
