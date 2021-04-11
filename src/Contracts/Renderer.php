<?php

namespace DarkGhostHunter\Laralerts\Contracts;

interface Renderer
{
    /**
     * Returns the rendered alerts as a single HTML string.
     *
     * @param  array|\DarkGhostHunter\Laralerts\Alert[]  $alerts
     *
     * @return string
     */
    public function render(array $alerts): string;
}
