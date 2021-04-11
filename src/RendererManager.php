<?php

namespace DarkGhostHunter\Laralerts;

use DarkGhostHunter\Laralerts\Contracts\Renderer;
use DarkGhostHunter\Laralerts\Renderers\BootstrapRenderer;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Manager;

/**
 * @method \DarkGhostHunter\Laralerts\Contracts\Renderer driver($driver = null)
 */
class RendererManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('laralerts.default');
    }

    /**
     * Creates a Bootstrap renderer.
     *
     * @return \DarkGhostHunter\Laralerts\Contracts\Renderer
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createBootstrapDriver(): Renderer
    {
        return new BootstrapRenderer($this->container->make(Factory::class));
    }
}
