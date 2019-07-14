<?php

namespace DarkGhostHunter\Laralerts\Tests;

use BadMethodCallException;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\AlertManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Session\Store;
use Mockery;
use Orchestra\Testbench\TestCase;

class AlertManagerTest extends TestCase
{
    use Concerns\RegistersPackage;

    /**
     * @var \DarkGhostHunter\Laralerts\AlertBag & \Mockery\MockInterface
     */
    protected $alertBag;

    /**
     * @var \Illuminate\Session\Store & \Mockery\MockInterface
     */
    protected $session;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var AlertManager
     */
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alertBag = Mockery::mock(AlertBag::class);
        $this->session = Mockery::mock(Store::class);
        $this->config = Mockery::mock(Repository::class);

        $this->config->expects('get')->with('laralerts.key')->andReturn('test_key');
        $this->config->expects('get')->with('laralerts.type')->andReturn('success');
        $this->config->expects('get')->with('laralerts.dismiss')->andReturnTrue();

        $this->manager = new AlertManager($this->alertBag, $this->session, $this->config);
    }

    public function testShouldNotFlashIfAlreadyFlashed()
    {
        $this->session->shouldReceive('isStarted')
            ->once()
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->once()
            ->with('test_key')
            ->andReturnFalse();

        $this->session->shouldNotReceive('flash');
        $this->session->shouldNotReceive('keep');

        $this->alertBag->shouldReceive('isDirty')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('shouldReflash')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('flush')
            ->once();

        $this->alertBag->shouldReceive('add')
            ->once()
            ->andReturnSelf();

        $this->session->shouldReceive('flash')
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->manager->message('test-message');
    }

    public function testWithOldShouldNotFlashButKeep()
    {
        $this->alertBag->shouldReceive('markForReflash')
            ->once();

        $this->session->shouldReceive('isStarted')
            ->twice()
            ->andReturnTrue();

        $this->session->shouldReceive('keep')
            ->with('test_key');

        $this->session->shouldNotReceive('flash');

        $this->alertBag->shouldReceive('isDirty')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('shouldReflash')
            ->once()
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->once()
            ->with('test_key')
            ->andReturnTrue();

        $this->alertBag->shouldReceive('add')
            ->once()
            ->andReturnSelf();

        $this->manager->withOld()->message('test-message');
    }

    public function testGetAndSetAlertBag()
    {
        $this->assertInstanceOf(AlertBag::class, $this->manager->getAlertBag());
        $this->assertEquals($this->alertBag, $this->manager->getAlertBag());

        $bag = new AlertBag();

        $this->manager->setAlertBag($bag);
        $this->assertEquals($bag, $this->manager->getAlertBag());
    }

    public function testGetAndSetSession()
    {
        $this->assertInstanceOf(Store::class, $this->manager->getSession());
        $this->assertEquals($this->session, $this->manager->getSession());

        $store = $this->mock(Store::class);

        $this->manager->setSession($store);
        $this->assertEquals($store, $this->manager->getSession());
    }

    public function testGetAndSetKey()
    {
        $this->assertEquals('test_key', $this->manager->getKey());

        $key = 'another_key';

        $this->manager->setKey($key);
        $this->assertEquals($key, $this->manager->getKey());
    }

    public function testGetAndSetType()
    {
        $this->assertEquals('success', $this->manager->getType());

        $type = 'test_type';

        $this->manager->setType($type);
        $this->assertEquals($type, $this->manager->getType());
    }

    public function testGetAndSetDismiss()
    {
        $this->assertTrue($this->manager->getDismiss());

        $dismiss = false;

        $this->manager->setDismiss($dismiss);
        $this->assertFalse($this->manager->getDismiss());
    }

    public function testAdd()
    {
        $this->alertBag->shouldReceive('isDirty')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('shouldReflash')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('flush')
            ->once();

        $this->session->shouldReceive('isStarted')
            ->once()
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->once()
            ->with('test_key')
            ->andReturnFalse();

        $this->session->shouldReceive('flash')
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->alertBag->shouldReceive('add')
            ->once()
            ->with(Mockery::type(Alert::class));

        $this->manager->message('test-message');
    }

    public function testMake()
    {
        $this->alertBag->shouldNotReceive('add');
        $this->alertBag->shouldNotReceive('flush');
        $this->session->shouldNotReceive('flash');

        $this->assertInstanceOf(Alert::class, $this->manager->make('message', 'success', true, 'foo'));
    }

    public function testAddFromJson()
    {
        $array = ['message' => 'test-message', 'type' => 'test-type'];

        $json = json_encode($array);

        $this->alertBag->shouldReceive('isDirty')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('shouldReflash')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('flush')
            ->once();

        $this->session->shouldReceive('isStarted')
            ->once()
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->once()
            ->with('test_key')
            ->andReturnFalse();

        $this->session->shouldReceive('flash')
            ->once()
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->alertBag->shouldReceive('add')
            ->once()
            ->with(Mockery::type(Alert::class))
            ->andReturnUsing(function ($alert) { return $alert; });

        $added = $this->manager->addFromJson($json);

        $this->assertEquals(array_merge($array, ['dismiss' => null, 'classes' => null]), $added->toArray());
    }

    public function testAddFromArray()
    {
        $array = ['message' => 'test-message', 'type' => 'test-type'];

        $this->alertBag->shouldReceive('isDirty')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('shouldReflash')
            ->once()
            ->andReturnFalse();

        $this->alertBag->shouldReceive('flush')
            ->once();

        $this->session->shouldReceive('isStarted')
            ->once()
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->once()
            ->with('test_key')
            ->andReturnFalse();

        $this->session->shouldReceive('flash')
            ->once()
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->alertBag->shouldReceive('add')
            ->once()
            ->with(Mockery::type(Alert::class))
            ->andReturnUsing(function ($alert) { return $alert; });

        $added = $this->manager->addFromArray($array);

        $this->assertEquals(array_merge($array, ['dismiss' => null, 'classes' => null]), $added->toArray());
    }


    public function testBypassToAlert()
    {
        $this->alertBag->shouldReceive('isDirty')
            ->times(14)
            ->andReturnTrue();

        $this->session->shouldReceive('isStarted')
            ->times(14)
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->times(14)
            ->with('test_key')
            ->andReturnTrue();

        $this->alertBag->shouldReceive('add')
            ->times(14)
            ->with(Mockery::type(Alert::class))
            ->andReturnUsing(function ($alert) { return $alert; });

        $this->assertInstanceOf(Alert::class, $this->manager->message('foo'));
        $this->assertInstanceOf(Alert::class, $this->manager->raw('bar'));
        $this->assertInstanceOf(Alert::class, $this->manager->lang('baz'));
        $this->assertInstanceOf(Alert::class, $this->manager->dismiss());
        $this->assertInstanceOf(Alert::class, $this->manager->fixed());
        $this->assertInstanceOf(Alert::class, $this->manager->primary());
        $this->assertInstanceOf(Alert::class, $this->manager->secondary());
        $this->assertInstanceOf(Alert::class, $this->manager->success());
        $this->assertInstanceOf(Alert::class, $this->manager->danger());
        $this->assertInstanceOf(Alert::class, $this->manager->warning());
        $this->assertInstanceOf(Alert::class, $this->manager->info());
        $this->assertInstanceOf(Alert::class, $this->manager->light());
        $this->assertInstanceOf(Alert::class, $this->manager->dark());
        $this->assertInstanceOf(Alert::class, $this->manager->classes('test', 'test'));
    }

    public function testReceivesMacro()
    {
        $manager = clone $this->manager;

        $manager::macro('test_macro', function ($argument) {
            return $argument;
        });

        $this->assertEquals('test-argument', $manager->test_macro('test-argument'));
    }

    public function testExceptionOnBadMethod()
    {
        $this->expectException(BadMethodCallException::class);

        $this->manager->anything();
    }
}
