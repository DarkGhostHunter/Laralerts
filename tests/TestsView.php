<?php

namespace Tests;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

trait TestsView
{
    /** @var \Illuminate\Contracts\View\View */
    protected View $view;

    public function addTestView()
    {
        $factory = $this->app[Factory::class];
        $factory->addLocation(__DIR__);
        $this->view = $factory->make('test-view');
    }
}
