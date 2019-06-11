<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CleanupAlertsCache
{
    /**
     * Config Repository
     *
     * @var Config
     */
    protected $config;

    /**
     * Creates a new middleware instance
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /** {@inheritdoc} */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * After the response is assembled, remove the alerts from the session cache.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function terminate($request, $response)
    {
        $key = $this->config->get('laralerts.key');
        $session = $request->session();

        if ($session->has($key)) {
            $session->forget($key);
            $session->save();
        }
    }
}
