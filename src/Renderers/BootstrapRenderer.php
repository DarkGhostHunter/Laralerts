<?php

namespace DarkGhostHunter\Laralerts\Renderers;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Contracts\Renderer;
use Generator;
use Illuminate\Contracts\View\Factory;

class BootstrapRenderer implements Renderer
{
    use CompilesClasses;

    /**
     * View file for Bootstrap Alerts.
     *
     * @var string
     */
    protected const VIEW = 'laralerts::bootstrap.container';

    /**
     * Translation table for known types
     *
     * @var array|string[]
     */
    protected const TYPE_CLASSES = [
        'primary' => 'alert-primary',
        'secondary' => 'alert-secondary',
        'success' => 'alert-success',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        'light' => 'alert-light',
        'dark' => 'alert-dark',
    ];

    /**
     * Classes that should be added when dismissing the alert.
     *
     * @var array|string[]
     */
    protected const DISMISS_CLASSES = [
        'fade',
        'show',
        'alert-dismissible',
    ];

    /**
     * The alerts that should be rendered
     *
     * @var array|\DarkGhostHunter\Laralerts\Alert[]
     */
    protected array $alerts = [];

    /**
     * View Factory to render each alert.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected Factory $factory;

    /**
     * BootstrapRenderer constructor.
     *
     * @param  \Illuminate\Contracts\View\Factory  $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Returns the rendered alerts as a single HTML string.
     *
     * @param  array|\DarkGhostHunter\Laralerts\Alert[]  $alerts
     *
     * @return string
     */
    public function render(array $alerts): string
    {
        return $this->factory
            ->make(static::VIEW)
            ->with('alerts', iterator_to_array($this->getAlerts($alerts)))->render();
    }

    /**
     * Get the alerts prepared for inserting in the view.
     *
     * @param  array|\DarkGhostHunter\Laralerts\Alert[]  $alerts
     *
     * @return \Generator
     */
    protected function getAlerts(array $alerts): Generator
    {
        foreach ($alerts as $alert) {
            // Here we will discard those with empty messages.
            if (filled($alert->getMessage())) {
                yield static::prepareAlert($alert);
            }
        }
    }

    /**
     * Prepares the alert array.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     *
     * @return object
     */
    protected static function prepareAlert(Alert $alert): object
    {
        return (object)[
            'message' => $alert->getMessage(),
            'classes' => static::compileClasses($alert),
            'dismissible' => $alert->isDismissible(),
        ];
    }
}
