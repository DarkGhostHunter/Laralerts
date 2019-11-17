<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use DarkGhostHunter\Laralerts\AlertBag;
use Illuminate\Contracts\Session\Session;

class ExpireAlerts
{
    /**
     * The current Alert Bag
     *
     * @var \DarkGhostHunter\Laralerts\AlertBag
     */
    protected $alertBag;

    /**
     * The Session handler
     *
     * @var \Illuminate\Contracts\Session\Session
     */
    protected $session;

    /**
     * Create a new ExpireAlerts instance.
     *
     * @param  \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @param  \Illuminate\Contracts\Session\Session $session
     */
    public function __construct(AlertBag $alertBag, Session $session)
    {
        $this->alertBag = $alertBag;
        $this->session = $session;
    }

    /**
     * Handle the incoming Request
     *
     * @param $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Handle an outgoing response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse $response
     * @return void
     */
    public function terminate($request, $response)
    {
        // After the response is sent to the browser, we will age the alerts automatically
        // since they should be already rendered. With this we can know if in the next
        // Request these old alerts should be recovered to show them again or not.
        if ($this->session->isStarted()) {
            $this->alertBag->ageAlerts();
        }
    }
}
