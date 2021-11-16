<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

use function app;
use function array_merge;
use function in_array;

class StoreAlertsInSession
{
    /**
     * Laralerts Middleware constructor.
     *
     * @param  \DarkGhostHunter\Laralerts\Bag  $bag
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  string  $key
     */
    public function __construct(protected Bag $bag, protected Session $session, protected string $key)
    {
        //
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // If the session doesn't exist or hasn't started, skip the middleware.
        if (! $request->getSession()?->isStarted()) {
            return $next($request);
        }

        $this->sessionAlertsToBag();

        $response = $next($request);

        $this->bagAlertsToSession();

        return $response;
    }

    /**
     * Takes the existing alerts in the session and adds them to the bag.
     *
     * @return void
     */
    protected function sessionAlertsToBag(): void
    {
        // Retrieve both persistent and non-persistent alerts and add them.
        app(Bag::class)->add(array_merge(
            $this->session->get("$this->key.persistent", []),
            $this->session->get("$this->key.alerts", []),
        ));

        // Removing the alerts from the session ensures these don't duplicate.
        $this->session->forget($this->key);
    }

    /**
     * Move the alerts back to the session.
     *
     * @return void
     */
    protected function bagAlertsToSession(): void
    {
        [$persistent, $nonPersistent] = $this->bag->collect()
            ->filter(static function (Alert $alert): bool {
                return '' !== $alert->getMessage();
            })->partition(function (Alert $alert): bool {
                return in_array($alert->index, $this->bag->getPersisted(), true);
            });

        // Persistent keys will be put persistently into the session.
        if ($persistent->isNotEmpty()) {
            $this->session->put("$this->key.persistent", $persistent->all());
        }

        // Those not persistent will be flashed. These will live during the
        // current request or the next if the actual one is a redirection.
        // Once done these will magically disappear from the alerts bag.
        if ($nonPersistent->isNotEmpty()) {
            $this->session->flash("$this->key.alerts", $nonPersistent->all());
        }

        $this->bag->flush();
    }
}
