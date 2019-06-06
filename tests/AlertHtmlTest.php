<?php

namespace DarkGhostHunter\Laralerts\Tests;

use ArrayIterator;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertBag;
use DarkGhostHunter\Laralerts\AlertsHtml;
use Illuminate\Support\HtmlString;
use Illuminate\View\Factory;
use Mockery;
use Orchestra\Testbench\TestCase;

class AlertHtmlTest extends TestCase
{
    use Concerns\RegistersPackage;

    /** @var \Illuminate\View\Factory & \Mockery\MockInterface */
    protected $mockViewFactory;

    /** @var \DarkGhostHunter\Laralerts\AlertBag & \Mockery\MockInterface */
    protected $mockBag;

    /** @var \DarkGhostHunter\Laralerts\AlertsHtml */
    protected $alertHtml;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockViewFactory = $this->mock(Factory::class);
        $this->mockBag = $this->mock(AlertBag::class);

        $this->alertHtml = new AlertsHtml($this->mockBag, $this->mockViewFactory);
    }

    public function testToHtml()
    {
        $this->mockBag->shouldReceive('doesntHavealerts')
            ->twice()
            ->andReturnFalse();

        $this->mockBag->shouldReceive('getIterator')
            ->twice()
            ->andReturn(new ArrayIterator([new Alert, new Alert]));

        $this->mockViewFactory->shouldReceive('make')
            ->times(4)
            ->with('laralerts::alert', Mockery::type('array'))
            ->andReturnSelf();

        $this->mockViewFactory->shouldReceive('make')
            ->twice()
            ->with('laralerts::alerts', Mockery::type('array'))
            ->andReturnSelf();

        $this->mockViewFactory->shouldReceive('render')
            ->times(6)
            ->withNoArgs()
            ->andReturn($html = '<div></div>');

        $string = $this->alertHtml->toHtml();

        $this->assertEquals($string, $html);
        $this->assertInstanceOf(HtmlString::class, $string);
        $this->assertEquals($string, (string)$this->alertHtml);
    }

    public function testToHtmlRendersNothingWhenEmptyAlerts()
    {
        $this->mockBag->shouldReceive('doesntHaveAlerts')
            ->once()
            ->andReturnTrue();

        $this->mockBag->shouldNotReceive('getIterator');
        $this->mockViewFactory->shouldNotReceive('make');
        $this->mockViewFactory->shouldNotReceive('render');

        $this->assertEmpty($this->alertHtml->toHtml());
    }
}