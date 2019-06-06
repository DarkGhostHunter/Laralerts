<?php

namespace DarkGhostHunter\Laralerts\Tests;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use Orchestra\Testbench\TestCase;

class AlertBagTest extends TestCase
{
    use Concerns\RegistersPackage;

    public function testGetAndSetAlerts()
    {
        $bag = new AlertBag;

        $this->assertEmpty($bag->getAlerts());

        $alerts = [
            new Alert, new Alert
        ];

        $bag->setAlerts($alerts);
        $this->assertEquals($alerts, $bag->getAlerts());
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

    public function testAddAlert()
    {
        $bag = new AlertBag;

        $bag->add($alert = new Alert);

        $this->assertEquals([$alert], $bag->getAlerts());
    }

    public function testRemoveFirst()
    {
        $bag = new AlertBag();

        $bag->setAlerts([
            new Alert('foo'), $bar = new Alert('bar'),
        ]);

        $bag->removeFirst();

        $this->assertEquals([$bar], $bag->getAlerts());
    }

    public function testRemoveLast()
    {
        $bag = new AlertBag();

        $bag->setAlerts([
            $foo = new Alert('foo'), new Alert('bar'),
        ]);

        $bag->removeLast();

        $this->assertEquals([$foo], $bag->getAlerts());
    }

    public function testFlush()
    {
        $bag = new AlertBag();

        $bag->setAlerts([
            new Alert('foo'), new Alert('bar'),
        ]);

        $bag->flush();

        $this->assertEquals([], $bag->getAlerts());
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

        $bag->setAlerts($alerts = [
            $foo = new Alert('foo'), $bar = new Alert('bar'), $baz = new Alert('baz')
        ]);

        $serialized = serialize($bag);

        $unserialize = unserialize($serialized);

        $this->assertEquals($alerts, $unserialize->getAlerts());
    }

    public function testJson()
    {
        $bag = new AlertBag();

        $bag->setAlerts($alerts = [
            $foo = new Alert('foo'), $bar = new Alert('bar'), $baz = new Alert('baz')
        ]);

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