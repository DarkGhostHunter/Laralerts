<?php

namespace DarkGhostHunter\Laralerts\Http\Middleware;

use Closure;
use DarkGhostHunter\Laralerts\Bag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function config;
use function data_get;
use function data_set;

class AddAlertsToJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $key
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next, string $key = null): JsonResponse|Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse && $response->isSuccessful()) {
            $key ??= config('laralerts.key');

            $data = $response->getData();

            if (null === data_get($data, $key)) {
                $response->setData(data_set($data, $key, app(Bag::class)->collect()->toArray()));
            }
        }

        return $response;
    }
}
