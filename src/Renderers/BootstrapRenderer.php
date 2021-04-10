<?php

namespace DarkGhostHunter\Laralerts\Renderers;

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Contracts\Renderer;
use Generator;
use Illuminate\Contracts\View\Factory;

class BootstrapRenderer implements Renderer
{
    use ReplacesLinks;

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
    protected const KNOWN_TYPES = [
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
            ->make(static::VIEW)->with('alerts', iterator_to_array($this->getAlerts()))->render();
    }

    /**
     * Get the alerts prepared for inserting in the view.
     *
     * @return \Generator
     */
    protected function getAlerts(): Generator
    {
        foreach ($this->alerts as $alert) {
            if ($alert->isRender()) {
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
            'message' => static::compileMessage($alert),
            'classes' => static::compileClasses($alert),
            'dismissible' => $alert->isDismissible(),
        ];
    }

    /**
     * Compiles the alert message.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     *
     * @return string
     */
    protected static function compileMessage(Alert $alert): string
    {
        return static::replaceLinks($alert->getMessage(), $alert->getLinks());
    }

    /**
     * Returns a list of classes as a string.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     *
     * @return string
     */
    protected static function compileClasses(Alert $alert): string
    {
        $classes = [];

        foreach ($alert->getTypes() as $type) {
            $classes[] = static::KNOWN_TYPES[$type] ?? $type;
        }

        if ($alert->isDismissible()) {
            $classes = array_merge($classes, static::DISMISS_CLASSES);
        }

        return implode(' ', $classes);
    }
}
