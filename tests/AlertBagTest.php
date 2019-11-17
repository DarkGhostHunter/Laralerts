<?php

namespace DarkGhostHunter\Laralerts\Tests;

use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;

class AlertBagTest extends TestCase
{
    use Concerns\RegistersPackage;

    protected $original;

    protected function setUp() : void
    {
        parent::setUp();

        $this->original = Alert::getTypes();

        Alert::setTypes([
            'baz' => 'alert-baz'
        ]);
    }

    protected function tearDown() : void
    {
        parent::tearDown();

        Alert::setTypes($this->original);
    }

    public function testGetAndSetDirty()
    {
        $bag = new AlertBag;

        $this->assertFalse($bag->isDirty());

        $bag->setDirty(true);

        $this->assertTrue($bag->isDirty());
    }

    public function testGetAndSetAlerts()
    {
        $bag = new AlertBag;

        $this->assertEmpty($bag->getAlerts());

        $this->assertFalse($bag->isDirty());

        $alerts = [
            new Alert, new Alert
        ];

        $bag->setAlerts($alerts);
        $this->assertEquals($alerts, $bag->getAlerts());

        $this->assertTrue($bag->isDirty());
    }

    public function testGetOldAlerts()
    {
        $bag = new AlertBag;
        $bag->setAlerts($alerts = [
            $message = new Alert('foo'),
            $type_a = new Alert('bar', 'baz'),
            $type_b = new Alert('qux', 'baz'),
            $both = new Alert('foo', 'baz'),
        ]);

        $bag->ageAlerts();

        $this->assertEquals($bag->getOld(), $alerts);
    }

    public function testReflashOldAlerts()
    {
        $bag = new AlertBag;
        $bag->setAlerts($alerts = [
            $message = new Alert('foo'),
            $type_a = new Alert('bar', 'baz'),
            $type_b = new Alert('qux', 'baz'),
            $both = new Alert('foo', 'baz'),
        ]);

        $this->assertEquals($alerts, $bag->getAlerts());

        $bag->ageAlerts();

        $this->assertEmpty($bag->getAlerts());

        $bag->add($alert = new Alert('qux'));

        $bag->reflash();

        $this->assertEquals(array_merge($alerts, [$alert]), $bag->getAlerts());

        $this->assertEmpty($bag->getOld());
    }

    public function testHasAlerts()
    {
        $bag = new AlertBag;

        $this->assertFalse($bag->hasAlerts());
        $this->assertTrue($bag->doesntHaveAlerts());

        $bag->setAlerts([
            new Alert, new Alert
        ]);

        $this->assertTrue($bag->hasAlerts());
        $this->assertFalse($bag->doesntHaveAlerts());
    }

    public function testFilterAlertsByMessage()
    {
        $bag = new AlertBag;
        $bag->setAlerts([
            $message = new Alert('foo'),
            $type_a = new Alert('bar', 'baz'),
            $type_b = new Alert('qux', 'baz'),
            $both = new Alert('foo', 'baz'),
        ]);

        $this->assertEquals([
            $message,
            $both
        ], $bag->filterByMessage('foo'));

        $this->assertEquals([
            $type_a
        ], $bag->filterByMessage('bar'));

        $this->assertEquals([
            $both
        ], $bag->filterByMessage('foo', 'baz'));
    }

    public function testFiltersAlertsByType()
    {
        $bag = new AlertBag;

        $bag->setAlerts([
            $message = new Alert('foo'),
            $type_a = new Alert('bar', 'baz'),
            $type_b = new Alert('qux', 'baz'),
            $both = new Alert('foo', 'baz'),
        ]);

        $this->assertEquals([], $bag->filterByType('foo'));

        $this->assertEquals([
            $type_a,
            $type_b,
            $both,
        ], $bag->filterByType('baz'));
    }

    public function testAddAlert()
    {
        $bag = new AlertBag;

        $this->assertFalse($bag->isDirty());

        $bag->add($alert = new Alert);

        $this->assertEquals([$alert], $bag->getAlerts());

        $this->assertTrue($bag->isDirty());
    }

    public function testFlush()
    {
        $bag = new AlertBag();

        $this->assertFalse($bag->isDirty());

        $bag->setAlerts([
            new Alert('foo'), new Alert('bar'),
        ]);

        $bag->flush();

        $this->assertEquals([], $bag->getAlerts());

        $this->assertTrue($bag->isDirty());
    }

    public function testToArray()
    {
        $bag = new AlertBag();

        $bag->setAlerts([
            $foo = new Alert('foo'), $bar = new Alert('bar'),
        ]);

        $array = $bag->toArray();

        $this->assertEquals([
            $foo->toArray(),
            $bar->toArray(),
        ], $array);
    }

    public function testCountable()
    {
        $bag = new AlertBag();

        $bag->setAlerts([
            new Alert('foo'), new Alert('bar'), new Alert('baz')
        ]);

        $this->assertCount(3, $bag);
    }

    public function testIterator()
    {
        $bag = new AlertBag();

        $bag->setAlerts([
            new Alert('foo'), new Alert('bar'), new Alert('baz')
        ]);

        $string = '';

        foreach ($bag as $alert) {
            $string .= $alert->getMessage();
        }

        $this->assertEquals('foobarbaz', $string);
    }

    public function testSerialization()
    {
        $bag = new AlertBag();

        $this->assertFalse($bag->isDirty());

        $bag->setAlerts($alerts = [
            $foo = new Alert('foo'), $bar = new Alert('bar'), $baz = new Alert('baz')
        ]);

        $serialized = serialize($bag);

        $unserialize = unserialize($serialized);

        $this->assertEquals($alerts, $unserialize->getAlerts());

        $this->assertFalse($unserialize->isDirty());
    }

    public function testJson()
    {
        $bag = new AlertBag();

        $this->assertFalse($bag->isDirty());

        $bag->setAlerts($alerts = [
            $foo = new Alert('foo'), $bar = new Alert('bar'), $baz = new Alert('baz')
        ]);

        $this->assertTrue($bag->isDirty());

        $json = json_encode($bag->toArray());

        $this->assertJson(json_encode($bag));
        $this->assertEquals($json, json_encode($bag));
        $this->assertEquals($json, $bag->toJson());
        $this->assertEquals([
            [
                'message' => 'foo',
                'type' => null,
                'dismiss' => false,
                'classes' => null,
            ],
            [
                'message' => 'bar',
                'type' => null,
                'dismiss' => false,
                'classes' => null,
            ],
            [
                'message' => 'baz',
                'type' => null,
                'dismiss' => false,
                'classes' => null,
            ],
        ], json_decode(json_encode($bag), true));
    }

}
