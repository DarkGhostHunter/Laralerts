<?php

namespace DarkGhostHunter\Laralerts\Renderers;

trait ReplacesLinks
{
    /**
     * Replaces links from a message using an array of links.
     *
     * @param  string  $message
     * @param  array  $links
     *
     * @return string
     */
    protected static function replaceLinks(string $message, array $links) : string
    {
        foreach ($links as $key => $link) {
            $start = strpos('{' . $key . ':', $message);

            if ($start !== false) {
                $end = strpos('}', $message, $start);

                $message = substr($message, 0, $start)
                    . static::hyperlink($message, $link, $start, $end)
                    . substr($message, $end);
            }
        }

        return $message;
    }
    /**
     * Transforms a link into an HTML hyperlink tag.
     *
     * @param  string  $message
     * @param  string  $link
     * @param  int  $start
     * @param  int  $end
     *
     * @return string
     */
    protected static function hyperlink(string $message, string $link, int $start, int $end): string
    {
        $link = '<a href="' . $link . '"';

        if (strpos($message, ':', $start) === 0) {
            $link .= ' target="_blank"';
            $start++;
        }

        // If the link is not an URL, we will try to check if it's a named route.
        if (strpos('http', $link) === false) {
            $link = route($link);
        }

        return $link . '>' . substr($message, $start, $end) . '</a>';
    }
}
