<?php

namespace DarkGhostHunter\Laralerts\Renderers;

use DarkGhostHunter\Laralerts\Alert;
use Illuminate\Support\Arr;

use function array_column;
use function array_map;
use function array_merge;
use function array_push;
use function array_unique;
use function defined;
use function implode;

trait CompilesAlert
{
    /**
     * Prepares the alert array.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     *
     * @return object
     */
    public function compileAlert(Alert $alert): object
    {
        return (object) [
            'message'     => static::compileMessage($alert),
            'classes'     => static::compileClasses($alert),
            'dismissible' => $alert->isDismissible(),
        ];
    }

    /**
     * Parses the message, replacing each keyword by an HTML link.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
     * @return string
     */
    protected static function compileMessage(Alert $alert): string
    {
        return str_replace(
            array_map(
                static function (string $replace): string {
                    return '{' . $replace . '}';
                }, array_column($alert->getLinks(), 'replace')
            ),
            array_map(static function (object $link): string {
                return "<a href=\"$link->url\"".($link->blank ? ' target="_blank"' : '').">$link->replace</a>";
            }, $alert->getLinks()),
            $alert->getMessage()
        );
    }

    /**
     * Returns a list of classes as a string for a "classes" HTML tag.
     *
     * @param  \DarkGhostHunter\Laralerts\Alert  $alert
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
