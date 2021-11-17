<?php

namespace DarkGhostHunter\Laralerts\View\Component;

use DarkGhostHunter\Laralerts\Bag;
use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Contracts\Renderer;
use Illuminate\View\Component;

class LaralertsComponent extends Component
{
    /**
     * Laralerts constructor.
     *
     * @param  \DarkGhostHunter\Laralerts\Bag  $bag
     * @param  \DarkGhostHunter\Laralerts\Contracts\Renderer  $renderer
     * @param  array|string  $tags
     */
    public function __construct(protected Bag $bag, protected Renderer $renderer, protected array|string $tags = Alert::DEFAULT_TAGS)
    {
        $this->tags = (array) $tags;
    }

    /**
     * Get the view / view contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function render(): string
    {
        return $this->renderer->render(
            $this->bag->collect()->filter->hasAnyTag($this->tags)
        );
    }
}
