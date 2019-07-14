<?php

namespace DarkGhostHunter\Laralerts\Tests\Http\Middleware;

use DarkGhostHunter\Laralerts\Http\Middleware\AppendAlertsToJsonResponse;
use DarkGhostHunter\Laralerts\Tests\Concerns\RegistersPackage;
use Orchestra\Testbench\TestCase;

class AppendAlertsToJsonResponseTest extends TestCase
{
    use RegistersPackage;

    public function testInjectsAlerts()
    {
        $this->app->make('router')->get('test', function () {
            alert('foo');
            return response()->json([
                'bar' => 'baz'
            ]);
        })->middleware(AppendAlertsToJsonResponse::class);

        $this->getJson('test')->assertExactJson([
            'bar' => 'baz',
            '_alerts' => [
                [
                    'message' => 'foo',
                    'type' => null,
                    'dismiss' => false,
                    'classes' => null,
                ]
            ]
        ])->assertSessionMissing('_alerts');
    }

    public function testDoesntInjectJsonWhenKeyIsPresent()
    {
        $this->app->make('router')->get('test', function () {
            alert('foo');

            return response()->json([
                'bar' => 'baz',
                '_alerts' => 'good'
            ]);
        })->middleware(AppendAlertsToJsonResponse::class);

        $this->getJson('test')->assertExactJson([
            'bar' => 'baz',
            '_alerts' => 'good'
        ])->assertSessionMissing('_alerts');
    }

    public function testDoesntInjectJsonOnNormalResponse()
    {
        $this->app->make('router')->get('test', function () {
            alert('foo');

            return response(json_encode([
                'bar' => 'baz',
            ]));
        })->middleware(AppendAlertsToJsonResponse::class);

        $this->get('test')->assertExactJson([
            'bar' => 'baz',
        ])->assertSessionMissing('_alerts');

        $this->getJson('test')->assertExactJson([
            'bar' => 'baz',
        ])->assertSessionMissing('_alerts');
    }

    public function testMiddlewareParameter()
    {
        $this->app->make('router')->get('test', function () {
            alert('foo');

            return response()->json([
                'bar' => 'baz',
            ]);
        })->middleware(AppendAlertsToJsonResponse::class . ':foo');

        $this->getJson('test')->assertExactJson([
            'bar' => 'baz',
            'foo' => [
                [
                    'message' => 'foo',
                    'type' => null,
                    'dismiss' => false,
                    'classes' => null,
                ]
            ]
        ])->assertSessionMissing('_alerts');
    }

    public function testMiddlewareParameterWithDotNotation()
    {
        $this->app->make('router')->get('test', function () {
            alert('foo');

            return response()->json([
                'bar' => 'baz',
            ]);
        })->middleware(AppendAlertsToJsonResponse::class . ':foo.bar');

        $this->getJson('test')->assertExactJson([
            'bar' => 'baz',
            'foo' => [
                'bar' => [
                    [
                        'message' => 'foo',
                        'type' => null,
                        'dismiss' => false,
                        'classes' => null,
                    ]
                ]
            ]
        ])->assertSessionMissing('_alerts');
    }
}