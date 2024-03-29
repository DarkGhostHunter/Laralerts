<?php

namespace Tests\Http\Middleware;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;

class AppendAlertsToJsonResponseTest extends TestCase
{
    use RegistersPackage;
    use InteractsWithViews;

    public function test_adds_json_using_config_key(): void
    {
        $this->app->make('router')->get(
            'test',
            function () {
                alert('foo', 'bar', 'quz');
                return response()->json(['bar' => 'baz']);
            }
        )->middleware('laralerts.json');

        $this->get('test')->assertExactJson(
            [
                'bar' => 'baz',
                '_alerts' => [
                    [
                        'dismissible' => false,
                        'message' => 'foo',
                        'types' => ['bar', 'quz'],
                    ],
                ],
            ]
        );
    }

    public function test_doesnt_adds_alerts_if_key_already_present(): void
    {
        $this->app->make('router')->get(
            'test',
            function () {
                alert('foo');
                return response()->json(
                    [
                        'bar' => 'baz',
                        '_alerts' => 'good',
                    ]
                );
            }
        )->middleware('laralerts.json');

        $this->getJson('test')->assertExactJson(
            [
                'bar' => 'baz',
                '_alerts' => 'good',
            ]
        );
    }

    public function test_doesnt_adds_alerts_if_response_not_json(): void
    {
        $this->app->make('router')->get(
            'test',
            function () {
                alert('foo');

                return response(json_encode(['bar' => 'baz']));
            }
        )->middleware('laralerts.json');

        $this->get('test')
            ->assertExactJson(['bar' => 'baz']);
    }

    public function test_doesnt_adds_alerts_if_response_client_error(): void
    {
        $this->app->make('router')->get(
            'test',
            function () {
                alert('foo');

                return response()->json(['bar' => 'baz'], 400);
            }
        )->middleware('laralerts.json');

        $this->get('test')
            ->assertExactJson(['bar' => 'baz']);
    }

    public function test_doesnt_adds_alerts_if_response_server_error(): void
    {
        $this->app->make('router')->get(
            'test',
            function () {
                alert('foo');

                return response()->json(['bar' => 'baz'], 500);
            }
        )->middleware('laralerts.json');

        $this->get('test')
            ->assertExactJson(['bar' => 'baz']);
    }

    public function test_adds_alerts_in_custom_key(): void
    {
        $this->app->make('router')->get(
            'test',
            function () {
                alert('foo', 'bar', 'quz');
                return response()->json(['bar' => 'baz']);
            }
        )->middleware('laralerts.json:foo.bar.quz');

        $this->get('test')
            ->assertExactJson(
                [
                    'bar' => 'baz',
                    'foo' => [
                        'bar' => [
                            'quz' => [
                                [
                                    'dismissible' => false,
                                    'message' => 'foo',
                                    'types' => ['bar', 'quz'],
                                ],
                            ]
                        ]
                    ]
                ]
            );
    }

    public function test_doesnt_replaces_custom_alerts_key(): void
    {
        $this->app->make('router')->get(
            'test',
            function () {
                alert('foo', 'bar', 'quz');
                return response()->json(['bar' => 'baz']);
            }
        )->middleware('laralerts.json:bar');

        $this->get('test')->assertExactJson(['bar' => 'baz']);
    }
}
