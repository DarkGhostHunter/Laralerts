<?php

namespace Tests\Views\Components;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use DarkGhostHunter\Laralerts\Contracts\Renderer;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;
use Tests\TestsView;

class LaralertsComponentTest extends TestCase
{
    use RegistersPackage;
    use TestsView;

    protected Bag $bag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(function () {
            $this->addTestView();

            $this->bag = $this->app[Bag::class];
        });
    }

    public function test_doesnt_renders_without_alerts()
    {
        static::assertEmpty($this->bag->all());

        static::assertEquals(<<<'EOT'
<div class="container">
    </div>

EOT
        , $this->view->render());
    }

    public function test_renders_alerts()
    {
        $render = $this->mock(Renderer::class);

        $render->shouldReceive('render')
            ->once()
            ->withArgs(function (array $alerts) {
                static::assertCount(1, $alerts);
                static::assertInstanceOf(Alert::class, $alerts[0]);

                return true;
            })
            ->andReturn('<foo>bar</foo>');

        alert('foo', 'bar');

        static::assertEquals(<<<'EOT'
<div class="container">
    <foo>bar</foo></div>

EOT
            , $this->view->render());
    }
}
