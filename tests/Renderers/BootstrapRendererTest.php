<?php

namespace Tests\Renderers;

use DarkGhostHunter\Laralerts\Bag;
use Orchestra\Testbench\TestCase;
use Tests\RegistersPackage;
use Tests\TestsView;

class BootstrapRendererTest extends TestCase
{
    use RegistersPackage;
    use TestsView;

    protected Bag $bag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(
            function () {
                $this->addTestView();
                $this->bag = $this->app[Bag::class];
            }
        );
    }

    public function test_renders_bootstrap_alert()
    {
        alert(
            'A Bootstrap alert',
            'primary',
            'secondary',
            'success',
            'danger',
            'warning',
            'info',
            'light',
            'dark',
            'foo',
            'bar',
            'dismiss'
        );

        static::assertEquals(
            <<<'EOT'
<div class="container">
    <div class="alerts">
        <div class="alert alert-primary alert-secondary alert-success alert-danger alert-warning alert-info alert-light alert-dark foo bar dismiss" role="alert">
    A Bootstrap alert
    </div>
    </div>
</div>

EOT
            ,
            $this->view->render()
        );
    }

    public function test_renders_dismissible_alert()
    {
        alert('A Bootstrap Alert', 'success', 'dark')->dismiss();

        static::assertEquals(
            <<<'EOT'
<div class="container">
    <div class="alerts">
        <div class="alert alert-success alert-dark fade show alert-dismissible" role="alert">
    A Bootstrap Alert
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    </div>
</div>

EOT
            ,
            $this->view->render()
        );
    }

    public function test_eliminates_duplicate_classes()
    {
        alert('A Bootstrap Alert', 'success', 'success', 'foo', 'foo', 'foo', 'bar', 'alert-dismissible')->dismiss();

        static::assertEquals(
            <<<'EOT'
<div class="container">
    <div class="alerts">
        <div class="alert alert-success foo bar alert-dismissible fade show" role="alert">
    A Bootstrap Alert
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    </div>
</div>

EOT
            ,
            $this->view->render()
        );
    }
}
