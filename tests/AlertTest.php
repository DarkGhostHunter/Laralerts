<?php

namespace Tests;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Orchestra\Testbench\TestCase;

use function alert;
use function app;

class AlertTest extends TestCase
{
    use RegistersPackage;

    public function test_creates_default_instance(): void
    {
        $alert = alert()->new();

        static::assertEmpty($alert->getMessage());
        static::assertEmpty($alert->getTypes());
        static::assertFalse($alert->isDismissible());
    }

    public function test_alert_can_receive_empty_message(): void
    {
        static::assertSame('', alert()->new()->getMessage());
        static::assertSame('', alert()->message('')->getMessage());
        static::assertSame('', alert()->raw('')->getMessage());
        static::assertSame('', alert()->trans('')->getMessage());
        static::assertSame('', alert()->transChoice('', 10)->getMessage());
    }

    public function test_alert_set_escaped_message(): void
    {
        $alert = alert()->new();

        $alert->message('❤ <script></script>');

        static::assertEquals('❤ &lt;script&gt;&lt;/script&gt;', $alert->getMessage());
    }

    public function test_alert_set_types(): void
    {
        $alert = alert()->new();

        $alert->types('foo', 'bar', 'quz');

        static::assertEquals(['foo', 'bar', 'quz'], $alert->getTypes());
    }

    public function test_alert_set_raw_message(): void
    {
        $alert = alert()->new();

        $alert->raw('❤ <script></script>');

        static::assertEquals('❤ <script></script>', $alert->getMessage());
    }

    public function test_alert_translates_message(): void
    {
        Lang::shouldReceive('get')
            ->once()
            ->with('test-key', ['foo' => 'bar'], 'test_lang')
            ->andReturn('test-translation');

        $alert = alert()->new();

        $alert->trans('test-key', ['foo' => 'bar'], 'test_lang');

        static::assertEquals('test-translation', $alert->getMessage());
    }

    public function test_alert_translates_pluralizable_message(): void
    {
        Lang::shouldReceive('choice')
            ->once()
            ->with('test-key', 10, ['foo' => 'bar'], 'test_lang')
            ->andReturn('test-translation');

        $alert = alert()->new();

        $alert->transChoice('test-key', 10, ['foo' => 'bar'], 'test_lang');

        static::assertEquals('test-translation', $alert->getMessage());
    }

    public function test_alert_receives_link_away(): void
    {
        $alert = alert()->new()->message('foo {bar} baz')->away('bar', 'https://foo-bar.com');

        static::assertEquals(
            [(object) ['replace' => 'bar', 'url' => 'https://foo-bar.com', 'blank' => true]],
            $alert->getLinks()
        );
    }

    public function test_alert_receives_link_to(): void
    {
        URL::shouldReceive('to')
            ->with('/foo-bar', [], false)
            ->andReturn('http://localhost/foo-bar');

        $alert = alert()->new()->message('foo {bar} baz')->to('bar', '/foo-bar');

        static::assertEquals(
            [(object) ['replace' => 'bar', 'url' => 'http://localhost/foo-bar', 'blank' => false]],
            $alert->getLinks()
        );
    }

    public function test_alert_receives_link_route(): void
    {
        URL::shouldReceive('route')
            ->with('test', [], true)
            ->andReturn('http://localhost/test');

        $alert = alert()->new()->message('foo {bar} baz')->route('bar', 'test');

        static::assertEquals(
            [(object) ['replace' => 'bar', 'url' => 'http://localhost/test', 'blank' => false]],
            $alert->getLinks()
        );
    }

    public function test_alert_receives_link_action(): void
    {
        URL::shouldReceive('action')
            ->with('DummyController@action', [], true)
            ->andReturn('http://localhost/test');

        $alert = alert()->new()->message('foo {bar} baz')->action('bar', 'DummyController@action');

        static::assertEquals(
            [(object) ['replace' => 'bar', 'url' => 'http://localhost/test', 'blank' => false]],
            $alert->getLinks()
        );
    }

    public function test_alert_is_dismissible(): void
    {
        $alert = alert()->new();

        static::assertFalse($alert->isDismissible());

        $alert->dismiss();

        static::assertTrue($alert->isDismissible());
    }

    public function test_alert_to_array(): void
    {
        $alert = alert()->new();

        $alert->message('foo')
            ->types('foo', 'bar')
            ->dismiss()
            ->persistAs('baz');

        static::assertEquals(
            [
                'message'     => 'foo',
                'types'       => ['foo', 'bar'],
                'dismissible' => true,
            ],
            $alert->toArray()
        );
    }

    public function test_array_to_json(): void
    {
        $alert = alert()->new();

        $alert->message('foo')
            ->types('foo', 'bar')
            ->dismiss()
            ->persistAs('baz');

        static::assertJson(json_encode($alert));
        static::assertEquals(
            '{"message":"foo","types":["foo","bar"],"dismissible":true}',
            $alert->toJson()
        );
    }

    public function test_alert_from_json(): void
    {
        $alert = Alert::fromArray(
            [
                'message'     => 'foo',
                'types'       => ['foo', 'bar'],
                'dismissible' => true,
                'persist_key' => 'baz',
            ]
        );

        static::assertEquals('foo', $alert->getMessage());
        static::assertEquals(['foo', 'bar'], $alert->getTypes());
        static::assertTrue($alert->isDismissible());
    }

    public function test_abandons_itself(): void
    {
        $alert = alert()->new();

        $alert->message('foo')
            ->types('foo', 'bar')
            ->dismiss()
            ->persistAs('baz');

        static::assertNotEmpty(app(Bag::class)->getPersisted());

        $alert->abandon();

        static::assertEmpty(app(Bag::class)->getPersisted());
    }

    public function test_tags(): void
    {
        $alert = alert()->new();

        static::assertSame(['default'], $alert->getTags());

        $alert->tag('foo', 'bar');

        static::assertSame(['foo', 'bar'], $alert->getTags());
    }

    public function test_to_string(): void
    {
        $alert = (new Alert(app(Bag::class)))->message('foo')->types('bar');

        static::assertEquals('{"message":"foo","types":["bar"],"dismissible":false}', (string)$alert);
    }
}
