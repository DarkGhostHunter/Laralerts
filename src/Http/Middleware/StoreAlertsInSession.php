<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\RedirectResponse;
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

        $this->bagAlertsToSession($response instanceof RedirectResponse);

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
     * @param  bool  $flashIfRedirect
     * @return void
     */
    protected function bagAlertsToSession(bool $flashIfRedirect): void
    {
        [$persistent, $nonPersistent] = $this->bag->collect()
            ->partition(function (Alert $alert): bool {
                return in_array($alert->index, $this->bag->getPersisted(), true);
            });

        // Persistent keys will be put persistently into the session.
        if ($persistent->isNotEmpty()) {
            $this->session->put("$this->key.persistent", $persistent->all());
        }

        // Non-persistent will be flashed if the response is as redirection.
        // This way we allow the next response from the app to have these
        // alerts without having to manually flash them from the app.
        if ($flashIfRedirect && $nonPersistent->isNotEmpty()) {
            $this->session->flash("$this->key.alerts", $nonPersistent->all());
        }

        $this->bag->flush();
    }
}
