<?php

namespace Tests\Views\Components;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use DarkGhostHunter\Laralerts\Contracts\Renderer;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;

class LaralertsComponentTest extends TestCase
{
    use RegistersPackage;
    use InteractsWithViews;

    protected Bag $bag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(function () {
            $this->bag = $this->app[Bag::class];
        });
    }

    public function test_doesnt_renders_without_alerts(): void
    {
        static::assertEmpty($this->bag->collect());

        static::assertEquals(<<<'EOT'
<div class="container"></div>
EOT
        , (string) $this->blade('<div class="container"><x-laralerts /></div>')
        );
    }

    public function test_renders_alerts(): void
    {
        $render = $this->mock(Renderer::class);

        $render->shouldReceive('render')
            ->once()
            ->withArgs(function (Collection $alerts) {
                static::assertCount(1, $alerts);
                static::assertInstanceOf(Alert::class, $alerts->get(0));

                return true;
            })
            ->andReturn('<foo>bar</foo>');

        alert('foo', 'bar');

        static::assertEquals(<<<'EOT'
<div class="container"><foo>bar</foo></div>
EOT
            ,
            (string) $this->blade('<div class="container"><x-laralerts /></div>')
        );
    }
}
