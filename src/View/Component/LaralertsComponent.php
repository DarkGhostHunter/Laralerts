<?php

namespace DarkGhostHunter\Laralerts\View\Component;

use DarkGhostHunter\Laralerts\Bag;
use DarkGhostHunter\Laralerts\Contracts\Renderer;
use Illuminate\View\Component;

class LaralertsComponent extends Component
{
    /**
     * The renderer for the alerts.
     *
     * @var \DarkGhostHunter\Laralerts\Contracts\Renderer
     */
    protected Renderer $renderer;

    /**
     * Alerts bag.
     *
     * @var \DarkGhostHunter\Laralerts\Bag
     */
    protected Bag $bag;

    /**
     * Laralerts constructor.
     *
     * @param  \DarkGhostHunter\Laralerts\Bag  $bag
     * @param  \DarkGhostHunter\Laralerts\Contracts\Renderer  $renderer
     */
    public function __construct(Bag $bag, Renderer $renderer)
    {
        $this->renderer = $renderer;
        $this->bag = $bag;
    }

    /**
     * Get the view / view contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render(): string
    {
        return $this->renderer->render($this->bag->all());
    }
}
