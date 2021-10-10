<?php

namespace DarkGhostHunter\Laralerts\Contracts;

use Illuminate\Support\Collection;

interface Renderer
{
    /**
     * Returns the rendered alerts as a single HTML string.
     *
     * @param  \Illuminate\Support\Collection|\DarkGhostHunter\Laralerts\Alert[]  $alerts
     * @return string
     */
    public function render(Collection $alerts): string;
}
