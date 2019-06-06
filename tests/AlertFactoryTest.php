<?php

namespace DarkGhostHunter\Laralerts\Tests;

use BadMethodCallException;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\AlertFactory;
use Illuminate\Session\Store;
use Mockery;
use Orchestra\Testbench\TestCase;

class AlertFactoryTest extends TestCase
{
    use Concerns\RegistersPackage;

    /**
     * @var \DarkGhostHunter\Laralerts\AlertFactory & \Mockery\MockInterface
     */
    protected $factory;

    /** @var \Illuminate\Session\Store & \Mockery\MockInterface */
    protected $mockStore;

    /** @var \DarkGhostHunter\Laralerts\AlertBag & \Mockery\MockInterface */
    protected $mockBag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockStore = $this->mock(Store::class);
        $this->mockBag = $this->mock(AlertBag::class);

        $this->factory = new AlertFactory($this->mockBag, $this->mockStore, 'test-type', true);
    }

    public function testGetAndSetAlertBag()
    {
        $this->assertInstanceOf(AlertBag::class, $this->factory->getAlertBag());
        $this->assertEquals($this->mockBag, $this->factory->getAlertBag());

        $bag = new AlertBag();

        $this->factory->setAlertBag($bag);
        $this->assertEquals($bag, $this->factory->getAlertBag());
    }

    public function testGetAndSetStore()
    {
        $this->assertInstanceOf(Store::class, $this->factory->getStore());
        $this->assertEquals($this->mockStore, $this->factory->getStore());

        $store = $this->mock(Store::class);

        $this->factory->setStore($store);
        $this->assertEquals($store, $this->factory->getStore());
    }

    public function testAddAlert()
    {
        $this->mockBag->shouldReceive('add')
            ->once()
            ->with(Mockery::type(Alert::class))
            ->andReturnUsing(function ($alert) { return $alert; });

        $alert = new Alert;

        $added = $this->factory->add($alert);
        $this->assertEquals($alert, $added);
    }

    public function testAddFromJson()
    {
        $array = ['message'=> 'test-message', 'type' => 'test-type'];

        $json = json_encode($array);

        $this->mockBag->shouldReceive('add')
            ->once()
            ->with(Mockery::type(Alert::class))
            ->andReturnUsing(function ($alert) { return $alert; });

        $added = $this->factory->addFromJson($json);

        $this->assertEquals(array_merge($array, ['dismiss' => null, 'classes' => null]), $added->toArray());
    }

    public function testAddFromArray()
    {
        $array = ['message'=> 'test-message', 'type' => 'test-type'];

        $this->mockBag->shouldReceive('add')
            ->once()
            ->with(Mockery::type(Alert::class))
            ->andReturnUsing(function ($alert) { return $alert; });

        $added = $this->factory->addFromArray($array);

        $this->assertEquals(array_merge($array, ['dismiss' => null, 'classes' => null]), $added->toArray());
    }

    public function testMake()
    {
        $this->mockBag->shouldNotReceive('add');

        $alert = $this->factory->make(
            'test', 'type', true, 'classes'
        );

        $this->assertInstanceOf(Alert::class, $alert);
        $this->assertEquals([
            'message' => 'test',
            'type' => 'type',
            'dismiss' => true,
            'classes' => 'classes'
        ], $alert->toArray());
    }

    public function testBypassToAlert()
    {
        $this->mockBag->shouldReceive('add')
            ->times(14)
            ->with(Mockery::type(Alert::class))
            ->andReturnUsing(function ($alert) { return $alert; });

        $this->assertInstanceOf(Alert::class, $this->factory->message('foo'));
        $this->assertInstanceOf(Alert::class, $this->factory->raw('bar'));
        $this->assertInstanceOf(Alert::class, $this->factory->lang('baz'));
        $this->assertInstanceOf(Alert::class, $this->factory->dismiss());
        $this->assertInstanceOf(Alert::class, $this->factory->fixed());
        $this->assertInstanceOf(Alert::class, $this->factory->primary());
        $this->assertInstanceOf(Alert::class, $this->factory->secondary());
        $this->assertInstanceOf(Alert::class, $this->factory->success());
        $this->assertInstanceOf(Alert::class, $this->factory->danger());
        $this->assertInstanceOf(Alert::class, $this->factory->warning());
        $this->assertInstanceOf(Alert::class, $this->factory->info());
        $this->assertInstanceOf(Alert::class, $this->factory->light());
        $this->assertInstanceOf(Alert::class, $this->factory->dark());
        $this->assertInstanceOf(Alert::class, $this->factory->classes('test', 'test'));
    }

    public function testExceptionOnInvalidMethodToBypass()
    {
        $this->expectException(BadMethodCallException::class);

        $this->factory->invalidType();
    }

}