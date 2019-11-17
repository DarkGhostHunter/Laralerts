<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use DarkGhostHunter\Laralerts\AlertBag;

class AppendAlertsToJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $key
     * @return mixed
     */
    public function handle($request, Closure $next, string $key = null)
    {
        $response = $next($request);

        // First, let's check if the Response is a JsonResponse. If not, then we will bail out.
        // We do this because the developer may be using this middleware in routes where the
        // response may be not necessarily be a JsonResponse, like when doing AJAX calls.
        if (! $response instanceof JsonResponse) {
            return $response;
        }

        $data = $response->getData(true);

        $key = $key ?? config('laralerts.key');

        // Second, let's extract the data, append the Alert Bag using the same Alerts Session
        // Key in the configuration, and finally put the modified data inside the response.
        // If the key in the data is being used, we won't do anything to avoid collision.
        if (! Arr::has($data, $key)) {
            $response->setData(Arr::add($data, $key, app(AlertBag::class)));
        }

        return $response;
    }
}
