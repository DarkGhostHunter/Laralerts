<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Manager;

/**
 * @method \DarkGhostHunter\Laralerts\Contracts\Renderer driver($driver = null)
 * @codeCoverageIgnore
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
    protected function createBootstrapDriver(): Contracts\Renderer
    {
        return new Renderers\BootstrapRenderer($this->container->make(Factory::class));
    }
}
