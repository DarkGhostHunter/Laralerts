<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use DarkGhostHunter\Laralerts\AlertBag;

class FlashAlertBagMiddleware
{
    /**
     * Alert Bag
     *
     * @var \DarkGhostHunter\Laralerts\AlertBag
     */
    protected $alertBag;

    /**
     * Key to use to flash the Alerts into the Session
     *
     * @var string
     */
    protected $sessionKey;

    /**
     * AddAlertBagToSession constructor.
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @param string $sessionKey
     */
    public function __construct(AlertBag $alertBag, string $sessionKey)
    {
        $this->alertBag = $alertBag;
        $this->sessionKey = $sessionKey;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->alertBag->hasAlerts()) {
            $request->session()->flash($this->sessionKey, $this->alertBag);
        }

        return $next($request);
    }
}