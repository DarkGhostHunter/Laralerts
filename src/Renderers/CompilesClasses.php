<?php

namespace DarkGhostHunter\Laralerts\Renderers;

use DarkGhostHunter\Laralerts\Alert;
use Illuminate\Support\Arr;

trait CompilesClasses
{
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
            if (defined('static::TYPE_CLASSES')) {
                array_push($classes, ...Arr::wrap(static::TYPE_CLASSES[$type] ?? $type));
            } else {
                $classes[] = $type;
            }
        }

        if (defined('static::DISMISS_CLASSES') && $alert->isDismissible()) {
            $classes = array_merge($classes, static::DISMISS_CLASSES);
        }

        return implode(' ', array_unique($classes));
    }
}
