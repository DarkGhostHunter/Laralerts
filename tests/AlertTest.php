<?php

namespace DarkGhostHunter\Laralerts\Tests;

use BadMethodCallException;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Lang;
use DarkGhostHunter\Laralerts\Alert;

class AlertTest extends TestCase
{
    use Concerns\RegistersPackage;

    public function testCreatesDefaultInstance()
    {
        $alert = new Alert;

        $this->assertNull($alert->getMessage());
        $this->assertNull($alert->getType());
        $this->assertNull($alert->getDismiss());
        $this->assertNull($alert->getClasses());
    }

    public function testCreatesAlertWithArguments()
    {
        $alert = new Alert('test-message', 'info', false, 'test-class');

        $this->assertEquals('test-message', $alert->getMessage());
        $this->assertEquals('info', $alert->getType());
        $this->assertFalse($alert->getDismiss());
        $this->assertEquals('test-class', $alert->getClasses());
    }

    public function testAcceptsInvalidTypeOnManualInstancing()
    {
        $alert = new Alert('test-message', 'invalid-type');

        $this->assertInstanceOf(Alert::class, $alert);
    }

    public function testExceptionOnInvalidTypeSet()
    {
        $this->expectException(BadMethodCallException::class);

        $alert = (new Alert())->setType('invalid-type');
    }

    public function testSetsMessage()
    {
        $alert = new Alert('dont-see-this', 'info', false, 'test-class');

        $alert->message('test-message');

        $this->assertEquals('test-message', $alert->getMessage());
    }

    public function testEncodedMessage()
    {
        $alert = new Alert;

        $alert->message('<script>alert("rofl")</script>');

        $this->assertEquals('&lt;script&gt;alert(&quot;rofl&quot;)&lt;/script&gt;', $alert->getMessage());
    }

    public function testRawMessage()
    {
        $alert = new Alert;

        $alert->raw('<script>alert("rofl")</script>');

        $this->assertEquals('<script>alert("rofl")</script>', $alert->getMessage());
    }

    public function testLocalizedMessage()
    {
        Lang::shouldReceive('get')
            ->once()
            ->with('test-key', ['foo' => 'bar'], 'test_lang')
            ->andReturn('test-translation');

        $alert = new Alert;

        $alert->lang('test-key', ['foo' => 'bar'], 'test_lang');

        $this->assertEquals('test-translation', $alert->getMessage());
    }

    public function testGetAndSetDismiss()
    {
        $alert = new Alert('test-message', 'info', false);

        $this->assertFalse($alert->getDismiss());
        $alert->setDismiss(true);
        $this->assertTrue($alert->getDismiss());

        $alert->setDismiss(false);
        $alert->dismiss();
        $this->assertTrue($alert->getDismiss());

        $alert->fixed();
        $this->assertFalse($alert->getDismiss());
    }

    public function testGetAndSetClasses()
    {
        $alert = new Alert('test-message', 'info', false, 'test-class');

        $this->assertEquals('test-class', $alert->getClasses());

        $alert->setClasses('class-one class-two');
        $this->assertEquals('class-one class-two', $alert->getClasses());

        $alert->classes('one', 'two');
        $this->assertEquals('one two', $alert->getClasses());

        $alert->classes(['one', 'two']);
        $this->assertEquals('one two', $alert->getClasses());
    }

    public function testToArray()
    {
        $alert = new Alert('test-message', 'info', false, 'test-class');

        $array = $alert->toArray();

        $this->assertEquals([
            'message' => 'test-message',
            'type' => 'info',
            'dismiss' => false,
            'classes' => 'test-class',
        ], $array);
    }

    public function testSerialization()
    {
        $alert = new Alert('test-message', 'info', false, 'test-class');

        $serialized = serialize($alert);

        $unserialize = unserialize($serialized);

        $this->assertInstanceOf(Alert::class, $unserialize);
        $this->assertEquals('test-message', $unserialize->getMessage());
        $this->assertEquals('info', $unserialize->getType());
        $this->assertFalse($unserialize->getDismiss());
        $this->assertEquals('test-class', $unserialize->getClasses());
    }

    public function testGetSetTypes()
    {
        $original = Alert::getTypes();

        Alert::setTypes(['foo', 'bar']);

        $this->assertEquals(['foo', 'bar'], Alert::getTypes());

        Alert::setTypes($original);
    }

    public function testDynamicTypeCalls()
    {
        $primary = (new Alert)->primary();
        $secondary = (new Alert)->secondary();
        $success = (new Alert)->success();
        $danger = (new Alert)->danger();
        $warning = (new Alert)->warning();
        $info = (new Alert)->info();
        $light = (new Alert)->light();
        $dark = (new Alert)->dark();

        $this->assertEquals('primary', $primary->getType());
        $this->assertEquals('secondary', $secondary->getType());
        $this->assertEquals('success', $success->getType());
        $this->assertEquals('danger', $danger->getType());
        $this->assertEquals('warning', $warning->getType());
        $this->assertEquals('info', $info->getType());
        $this->assertEquals('light', $light->getType());
        $this->assertEquals('dark', $dark->getType());
    }

    public function testDynamicCustomTypeCall()
    {
        $original = Alert::getTypes();

        Alert::setTypes(['foo', 'bar']);

        $foo = (new Alert)->foo();
        $bar = (new Alert)->bar();

        $this->assertEquals('foo', $foo->getType());
        $this->assertEquals('bar', $bar->getType());

        Alert::setTypes($original);
    }

    public function testExceptionOnInvalidDynamicCustomTypeCall()
    {
        $this->expectException(BadMethodCallException::class);

        $alert = new class extends Alert
        {
            protected static $types = ['foo', 'bar'];
        };

        $alert->baz();
    }

    public function testJson()
    {
        $alert = new Alert('test-message', 'info', false, 'test-class');

        $json = json_encode($alert->toArray());

        $this->assertJson(json_encode($alert));
        $this->assertEquals($json, json_encode($alert));
        $this->assertEquals($json, $alert->toJson());
        $this->assertEquals([
            'message' => 'test-message',
            'type' => 'info',
            'dismiss' => false,
            'classes' => 'test-class',
        ], json_decode(json_encode($alert), true));
    }

    public function testFromArray()
    {
        $array = [
            'type' => 'info',
            'message' => 'test-message',
            'classes' => 'test-class',
            'dismiss' => false,
        ];

        $alert = Alert::fromArray($array);

        $this->assertInstanceOf(Alert::class, $alert);

        $this->assertEquals([
            'message' => 'test-message',
            'type' => 'info',
            'dismiss' => false,
            'classes' => 'test-class',
        ], $alert->toArray());
    }

    public function testFromPartialArray()
    {
        $array = [
            'message' => 'test-message',
        ];

        $alert = Alert::fromArray($array);

        $this->assertInstanceOf(Alert::class, $alert);

        $this->assertEquals([
            'message' => 'test-message',
            'type' => null,
            'dismiss' => null,
            'classes' => null,
        ], $alert->toArray());
    }

    public function testFromJson()
    {
        $array = [
            'type' => 'info',
            'message' => 'test-message',
            'classes' => 'test-class',
            'dismiss' => false,
        ];

        $json = json_encode($array);

        $alert = Alert::fromJson($json);

        $this->assertInstanceOf(Alert::class, $alert);

        $this->assertEquals([
            'message' => 'test-message',
            'type' => 'info',
            'dismiss' => false,
            'classes' => 'test-class',
        ], $alert->toArray());
    }

    public function testFromPartialJson()
    {
        $array = [
            'message' => 'test-message',
        ];

        $json = json_encode($array);

        $alert = Alert::fromJson($json);

        $this->assertInstanceOf(Alert::class, $alert);

        $this->assertEquals([
            'message' => 'test-message',
            'type' => null,
            'dismiss' => null,
            'classes' => null,
        ], $alert->toArray());
    }
}
