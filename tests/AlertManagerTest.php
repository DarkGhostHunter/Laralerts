<?php

namespace DarkGhostHunter\Laralerts\Tests;

use Mockery;
use BadMethodCallException;
use Illuminate\Session\Store;
use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\AlertManager;

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

    /**
     * @var array
     */
    protected $original;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alertBag = Mockery::mock(AlertBag::class);
        $this->session = Mockery::mock(Store::class);

        $this->manager = new AlertManager($this->alertBag, $this->session, 'test_key', 'success', true);

        $this->original = Alert::getTypes();

        Alert::addTypes([
            'test-type' => 'class-test-type'
        ]);
    }

    protected function tearDown() : void
    {
        parent::tearDown();

        Alert::setTypes($this->original);
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

        $this->alertBag->shouldReceive('add')
            ->once()
            ->andReturnSelf();

        $this->session->shouldReceive('flash')
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->manager->message('test-message');
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

    public function testReflashesOldAlerts()
    {
        $this->alertBag->shouldReceive('reflash')
            ->once();

        $this->session->shouldReceive('isStarted')
            ->once()
            ->andReturnTrue();

        $this->session->shouldReceive('keep')
            ->once()
            ->with('test_key');

        $this->manager->reflash();
    }

    public function testDoesntReflashWhenSessionNotStarted()
    {
        $this->alertBag->shouldReceive('reflash')
            ->once();

        $this->session->shouldReceive('isStarted')
            ->once()
            ->andReturnFalse();

        $this->session->shouldNotReceive('keep');

        $this->manager->reflash();
    }

    public function testAddManyFromArray()
    {
        $this->session->shouldReceive('isStarted')
            ->times(3)
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->times(3)
            ->with('test_key')
            ->andReturnFalse();

        $this->session->shouldReceive('flash')
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->alertBag->shouldReceive('add')
            ->times(3)
            ->with(Mockery::type(Alert::class));

        $this->manager->addManyFromArray([
            ['message' => 'test-message', 'type' => 'test-type'],
            ['message' => 'test-message', 'type' => 'test-type'],
            ['message' => 'test-message', 'type' => 'test-type'],
        ]);
    }

    public function testAddManyFromArrayWithLocation()
    {
        $this->session->shouldReceive('isStarted')
            ->times(3)
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->times(3)
            ->with('test_key')
            ->andReturnFalse();

        $this->session->shouldReceive('flash')
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->alertBag->shouldReceive('add')
            ->times(3)
            ->with(Mockery::type(Alert::class));

        $this->manager->addManyFromArray([
            'foo' => [
                'bar' => [
                    ['message' => 'test-message', 'type' => 'test-type'],
                    ['message' => 'test-message', 'type' => 'test-type'],
                    ['message' => 'test-message', 'type' => 'test-type'],
                ]
            ]
        ], 'foo.bar');
    }

    public function testAddManyFromJson()
    {
        $this->session->shouldReceive('isStarted')
            ->times(3)
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->times(3)
            ->with('test_key')
            ->andReturnFalse();

        $this->session->shouldReceive('flash')
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->alertBag->shouldReceive('add')
            ->times(3)
            ->with(Mockery::type(Alert::class));

        $json = json_encode([
            ['message' => 'test-message', 'type' => 'test-type'],
            ['message' => 'test-message', 'type' => 'test-type'],
            ['message' => 'test-message', 'type' => 'test-type'],
        ]);

        $this->manager->addManyFromJson($json);
    }

    public function testAddManyFromJsonWithLocation()
    {
        $this->session->shouldReceive('isStarted')
            ->times(3)
            ->andReturnTrue();

        $this->session->shouldReceive('has')
            ->times(3)
            ->with('test_key')
            ->andReturnFalse();

        $this->session->shouldReceive('flash')
            ->with('test_key', Mockery::type(AlertBag::class));

        $this->alertBag->shouldReceive('add')
            ->times(3)
            ->with(Mockery::type(Alert::class));

        $json = json_encode([
            'foo' => [
                'bar' => [
                    ['message' => 'test-message', 'type' => 'test-type'],
                    ['message' => 'test-message', 'type' => 'test-type'],
                    ['message' => 'test-message', 'type' => 'test-type'],
                ]
            ]
        ]);

        $this->manager->addManyFromJson($json, 'foo.bar');
    }

    public function testMake()
    {
        $this->alertBag->shouldNotReceive('add');
        $this->session->shouldNotReceive('flash');

        $this->assertInstanceOf(Alert::class, $this->manager->make('message', 'success', true, 'foo'));
    }

    public function testAddFromJson()
    {
        $original = Alert::getTypes();

        Alert::setTypes([
            'test-type' => 'test-type-class'
        ]);

        $array = ['message' => 'test-message', 'type' => 'test-type'];

        $json = json_encode($array);

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

        Alert::setTypes($original);
    }

    public function testAddFromArray()
    {
        $original = Alert::getTypes();

        Alert::setTypes([
            'test-type' => 'test-type-class'
        ]);

        $array = ['message' => 'test-message', 'type' => 'test-type'];

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

        Alert::setTypes($original);
    }


    public function testBypassToAlert()
    {
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

        $original = Alert::getTypes();
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
