<?php

namespace DarkGhostHunter\Laralerts\View\Component;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;
use DarkGhostHunter\Laralerts\Contracts\Renderer;
use Illuminate\View\Component;

class LaralertsComponent extends Component
{
    /**
     * Laralerts constructor.
     *
     * @param  \DarkGhostHunter\Laralerts\Bag  $bag
     * @param  \DarkGhostHunter\Laralerts\Contracts\Renderer  $renderer
     */
    public function __construct(protected Bag $bag, protected Renderer $renderer)
    {
        //
    }

    /**
     * Get the view / view contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function render(): string
    {
        return $this->renderer->render($this->bag->collect()->filter(static function (Alert $alert): bool {
            return (bool) $alert->getMessage();
        }));
    }
}
