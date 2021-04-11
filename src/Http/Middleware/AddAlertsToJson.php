<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class AddAlertsToJson
{
    /**
     * Alerts Bag.
     *
     * @var \DarkGhostHunter\Laralerts\Bag
     */
    protected Bag $bag;

    /**
     * Application config.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected Repository $config;

    /**
     * AddAlertsToJson constructor.
     *
     * @param  \DarkGhostHunter\Laralerts\Bag  $bag
     * @param  \Illuminate\Contracts\Config\Repository  $config
     */
    public function __construct(Bag $bag, Repository $config)
    {
        $this->bag = $bag;
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $key
     *
     * @return mixed
     */
    public function handle($request, Closure $next, string $key = null)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse && !$response->isClientError() && !$response->isServerError()) {
            $key = $key ?? $this->config->get('laralerts.key');

            $data = $response->getData(true);

            // If the outgoing data already has the key, don't replace it.
            if (!Arr::has($data, $key)) {
                $response->setData(Arr::add($data, $key, $this->alertsToArray()));
            }
        }

        return $response;
    }

    /**
     * Transforms the Alerts of the Bag to a play array.
     *
     * @return array
     */
    protected function alertsToArray(): array
    {
        return array_map(
            static function (Alert $alert): array {
                return $alert->toArray();
            },
            $this->bag->all()
        );
    }
}
