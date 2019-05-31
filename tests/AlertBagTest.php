<?php

namespace DarkGhostHunter\Laralerts\Tests;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use DOMDocument;
use Orchestra\Testbench\TestCase;

class AlertBagTest extends TestCase
{
    use Concerns\RegistersPackage;

    /** @var AlertBag */
    protected $alertBag;

    /** @var Alert */
    protected $alert;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alertBag = new AlertBag();

        $this->alert = (new Alert())->message('test-message');
    }

    public function testGetAndSetAlerts()
    {
        $this->assertEmpty($this->alertBag->getAlerts());
        $this->alertBag->setAlerts($alerts = [$this->alert]);
        $this->assertEquals($alerts, $this->alertBag->getAlerts());
    }

    public function testAdd()
    {
        $this->alertBag->add($this->alert);
        $this->assertEquals([$this->alert], $this->alertBag->getAlerts());
    }

    public function testPrepend()
    {
        $this->alertBag->add($this->alert);

        $prepended = (new Alert())->message('prepended');

        $this->alertBag->prepend($prepended);

        $this->assertEquals($prepended, $this->alertBag->getAlerts()[0]);
        $this->assertCount(2, $this->alertBag->getAlerts());
    }

    public function testAppend()
    {
        $this->alertBag->add($this->alert);

        $appended = (new Alert())->message('prepended');

        $this->alertBag->append($appended);

        $this->assertEquals($appended, $this->alertBag->getAlerts()[1]);
        $this->assertCount(2, $this->alertBag->getAlerts());
    }

    public function testRemoveFirst()
    {
        $this->alertBag->add($this->alert);
        $this->alertBag->add($second = (new Alert())->message('second'));

        $this->assertCount(2, $this->alertBag->getAlerts());

        $this->alertBag->removeFirst();

        $this->assertEquals($second, $this->alertBag->getAlerts()[0]);
        $this->assertCount(1, $this->alertBag->getAlerts());
    }

    public function testRemoveLast()
    {
        $this->alertBag->add($this->alert);
        $this->alertBag->add($second = (new Alert())->message('second'));

        $this->assertCount(2, $this->alertBag->getAlerts());

        $this->alertBag->removeLast();

        $this->assertEquals($this->alert, $this->alertBag->getAlerts()[0]);
        $this->assertCount(1, $this->alertBag->getAlerts());
    }

    public function testHasAlerts()
    {
        $this->assertFalse($this->alertBag->hasAlerts());

        $this->alertBag->add($this->alert);
        $this->alertBag->add($second = (new Alert())->message('second'));

        $this->assertTrue($this->alertBag->hasAlerts());
    }

    public function testFlush()
    {
        $this->alertBag->add($this->alert);
        $this->alertBag->add((new Alert())->message('second'));

        $this->assertCount(2, $this->alertBag->getAlerts());

        $this->alertBag->flush();

        $this->assertCount(0, $this->alertBag->getAlerts());
    }

    public function testToArray()
    {
        $this->alertBag->add($this->alert);
        $this->alertBag->add((new Alert())->message('second'));

        $this->assertCount(2, $this->alertBag->toArray());
        $this->assertIsArray($this->alertBag->toArray());
        $this->assertIsArray($this->alertBag->toArray()[0]);
    }

    public function testCountable()
    {
        $this->alertBag->add($this->alert);
        $this->alertBag->add((new Alert())->message('second'));

        $this->assertCount(2, $this->alertBag);
    }

    public function testValidJSON()
    {
        $this->alertBag->add($this->alert);
        $this->alertBag->add((new Alert())->message('second'));

        $this->assertJson($this->alertBag->toJson());
    }

    public function testRendersToHTML()
    {
        $this->alertBag->add($this->alert);
        $this->alertBag->add((new Alert())->message('second'));

        $html = '<div class="alert" role="alert">test-message</div><div class="alert" role="alert">second</div>';

        $this->assertEquals($html, $this->alertBag->toHtml());
        $this->assertEquals($html, $this->alertBag->render());
        $this->assertEquals($html, (string)$this->alertBag);
    }

}