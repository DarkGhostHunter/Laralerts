<?php

namespace Tests;

use DarkGhostHunter\Laralerts\Alert;
use Illuminate\Support\Facades\Lang;
use Orchestra\Testbench\TestCase;

class AlertTest extends TestCase
{
    use RegistersPackage;

    public function test_creates_default_instance()
    {
        $alert = alert()->new();

        static::assertEmpty($alert->getMessage());
        static::assertEmpty($alert->getTypes());
        static::assertFalse($alert->isDismissible());
        static::assertFalse($alert->isPersistent());
        static::assertNull($alert->getPersistKey());
    }

    public function test_alert_set_escaped_message()
    {
        $alert = alert()->new();

        $alert->message('❤ <script></script>');

        static::assertEquals('❤ &lt;script&gt;&lt;/script&gt;', $alert->getMessage());
    }

    public function test_alert_set_types()
    {
        $alert = alert()->new();

        $alert->types('foo', 'bar', 'quz');

        static::assertEquals(['foo', 'bar', 'quz'], $alert->getTypes());
    }

    public function test_alert_set_raw_message()
    {
        $alert = alert()->new();

        $alert->raw('❤ <script></script>');

        static::assertEquals('❤ <script></script>', $alert->getMessage());
    }

    public function test_alert_translates_message()
    {
        Lang::shouldReceive('get')
            ->once()
            ->with('test-key', ['foo' => 'bar'], 'test_lang')
            ->andReturn('test-translation');

        $alert = alert()->new();

        $alert->trans('test-key', ['foo' => 'bar'], 'test_lang');

        static::assertEquals('test-translation', $alert->getMessage());
    }

    public function test_alert_is_dismissible()
    {
        $alert = alert()->new();

        $alert->dismiss();

        static::assertTrue($alert->isDismissible());
    }

    public function test_alert_is_not_dismissible()
    {
        $alert = alert()->new();

        $alert->dismiss(false);

        static::assertFalse($alert->isDismissible());

        $alert->dismiss(true);

        static::assertTrue($alert->isDismissible());
    }

    public function test_alert_to_array()
    {
        $alert = alert()->new();

        $alert->message('foo')
            ->types('foo', 'bar')
            ->dismiss()
            ->persistAs('baz');

        static::assertEquals(
            [
                'message' => 'foo',
                'types' => ['foo', 'bar'],
                'dismissible' => true,
            ],
            $alert->toArray()
        );
    }

    public function test_array_to_json()
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

    public function test_alert_from_json()
    {
        $alert = Alert::fromArray(
            [
                'message' => 'foo',
                'types' => ['foo', 'bar'],
                'dismissible' => true,
                'persistent' => 'baz',
            ]
        );

        static::assertEquals('foo', $alert->getMessage());
        static::assertEquals(['foo', 'bar'], $alert->getTypes());
        static::assertTrue($alert->isDismissible());
        static::assertEquals('baz', $alert->getPersistKey());
        static::assertTrue($alert->isPersistent());
    }
}
