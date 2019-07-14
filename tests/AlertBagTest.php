<?php

namespace DarkGhostHunter\Laralerts\Tests;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use Orchestra\Testbench\TestCase;

class AlertBagTest extends TestCase
{
    use Concerns\RegistersPackage;

    public function testGetAndSetDirty()
    {
        $bag = new AlertBag;

        $this->assertFalse($bag->isDirty());

        $bag->setDirty(true);

        $this->assertTrue($bag->isDirty());
    }

    public function testGetAndSetReflash()
    {
        $bag = new AlertBag;

        $this->assertFalse($bag->shouldReflash());

        $bag->markForReflash();

        $this->assertTrue($bag->shouldReflash());


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

        $this->assertFalse($bag->isDirty());

        $bag->add($alert = new Alert);

        $this->assertEquals([$alert], $bag->getAlerts());

        $this->assertTrue($bag->isDirty());
    }

    public function testRemoveFirst()
    {
        $bag = new AlertBag();

        $this->assertFalse($bag->isDirty());

        $bag->setAlerts([
            new Alert('foo'), $bar = new Alert('bar'),
        ]);

        $bag->removeFirst();

        $this->assertEquals([$bar], $bag->getAlerts());

        $this->assertTrue($bag->isDirty());
    }

    public function testRemoveLast()
    {
        $bag = new AlertBag();

        $this->assertFalse($bag->isDirty());

        $bag->setAlerts([
            $foo = new Alert('foo'), new Alert('bar'),
        ]);

        $bag->removeLast();

        $this->assertEquals([$foo], $bag->getAlerts());

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