<?php

namespace DarkGhostHunter\Laralerts\Tests;

use BadMethodCallException;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\AlertFactory;
use Illuminate\Session\Store as Session;
use Mockery;
use Orchestra\Testbench\TestCase;

class AlertFactoryTest extends TestCase
{
    use Concerns\RegistersPackage;

    /**
     * @var AlertBag & Mockery\Mock
     */
    protected $mockAlertBag;

    /**
     * @var Session & Mockery\Mock
     */
    protected $mockSession;

    /**
     * @var AlertFactory
     */
    protected $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockAlertBag = Mockery::mock(AlertBag::class);

        $this->mockSession = Mockery::mock(Session::class);

        $this->factory = new AlertFactory($this->mockAlertBag, $this->mockSession);

    }

    public function testDefaults()
    {
        $this->assertNull($this->factory->getDefaultAnimationClass());
        $this->assertNull($this->factory->getDefaultClasses());
        $this->assertFalse($this->factory->isDefaultDismissible());
        $this->assertTrue($this->factory->isDefaultShow());
        $this->assertNull($this->factory->getDefaultType());

        $this->factory->setDefaultAnimationClass($animationClass = 'test-animation');
        $this->factory->setDefaultClasses($classes = 'test-class');
        $this->factory->setDefaultDismissible($dismissible = true);
        $this->factory->setDefaultShow($show = false);
        $this->factory->setDefaultType($type = 'primary');

        $this->assertEquals($animationClass, $this->factory->getDefaultAnimationClass());
        $this->assertEquals([$classes], $this->factory->getDefaultClasses());
        $this->assertEquals($dismissible, $this->factory->isDefaultDismissible());
        $this->assertEquals($show, $this->factory->isDefaultShow());
        $this->assertEquals($type, $this->factory->getDefaultType());

        $alert = $this->factory->make();

        $this->assertInstanceOf(Alert::class, $alert);
        $this->assertEquals($animationClass, $alert->getAnimationClass());
        $this->assertEquals($classes, $alert->getClasses());
        $this->assertEquals($dismissible, $alert->isDismissible());
        $this->assertEquals($show, $alert->isShow());
        $this->assertEquals($type, $alert->getType());
    }

    public function testInvalidDefaultType()
    {
        $this->expectException(BadMethodCallException::class);
        $this->factory->setDefaultType('invalid-type');
    }

    public function testDefaultClassesArray()
    {
        $this->factory->setDefaultClasses(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $this->factory->getDefaultClasses());
    }

    public function testKey()
    {
        $this->mockAlertBag->shouldReceive('add')
            ->once()
            ->with(\Mockery::type(Alert::class))
            ->andReturn($this->mockAlertBag);

        $this->mockSession->shouldReceive('flash')
            ->once()
            ->with('test-key', $this->mockAlertBag)
            ->andReturnNull();

        $this->assertEquals('alerts', $this->factory->getKey());
        $this->factory->setKey($key = 'test-key');
        $this->assertEquals($key, $this->factory->getKey());

        $this->factory->add(new Alert());
    }

    public function testAlertBag()
    {
        $this->assertInstanceOf(AlertBag::class, $this->factory->getAlertBag());

        $randomAlertBag = new class extends AlertBag {};

        $this->factory->setAlertBag($randomAlertBag);

        $this->assertInstanceOf(get_class($randomAlertBag), $this->factory->getAlertBag());
    }

    public function testMakeAlert()
    {
        $this->assertInstanceOf(Alert::class, $this->factory->make());
    }

    public function testAddAlert()
    {
        $this->mockAlertBag->shouldReceive('add')
            ->twice()
            ->with(\Mockery::type(Alert::class))
            ->andReturn($this->mockAlertBag);

        $this->mockSession->shouldReceive('flash')
            ->twice()
            ->with($this->factory->getKey(), $this->mockAlertBag)
            ->andReturnNull();

        $alert = new Alert();

        $alert->message('test-message');

        $this->assertInstanceOf(Alert::class, $this->factory->add());
        $this->assertInstanceOf(Alert::class, $receivedAlert = $this->factory->add($alert));
        $this->assertEquals('test-message', $receivedAlert->getMessage());
    }

    public function testPassthroughsAlert()
    {
        $this->mockAlertBag->shouldReceive('add')
            ->with(\Mockery::type(Alert::class))
            ->andReturn($this->mockAlertBag);

        $this->mockSession->shouldReceive('flash')
            ->with($this->factory->getKey(), $this->mockAlertBag)
            ->andReturnNull();

        $this->assertInstanceOf(Alert::class, $this->factory->message('test'));
        $this->assertInstanceOf(Alert::class, $this->factory->lang('test'));
        $this->assertInstanceOf(Alert::class, $this->factory->primary());
        $this->assertInstanceOf(Alert::class, $this->factory->secondary());
        $this->assertInstanceOf(Alert::class, $this->factory->success());
        $this->assertInstanceOf(Alert::class, $this->factory->danger());
        $this->assertInstanceOf(Alert::class, $this->factory->warning());
        $this->assertInstanceOf(Alert::class, $this->factory->info());
        $this->assertInstanceOf(Alert::class, $this->factory->light());
        $this->assertInstanceOf(Alert::class, $this->factory->dark());
        $this->assertInstanceOf(Alert::class, $this->factory->dismissible());
        $this->assertInstanceOf(Alert::class, $this->factory->fixed());
        $this->assertInstanceOf(Alert::class, $this->factory->classes('test-class'));
    }

    public function testDoesNotPassthroughsAlert()
    {
        $this->expectException(BadMethodCallException::class);

        $this->mockAlertBag->shouldNotReceive('add');
        $this->mockSession->shouldNotReceive('flash');

        $this->factory->anInvalidMethod('with', 'invalid', 'arguments');
    }
}