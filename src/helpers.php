<?php

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\Bag;

if (!function_exists('alert')) {
    /**
     * Creates an Alert to render, or calls the Alert Bag without arguments.
     *
     * @param  string|null  $message
     * @param  string  ...$types
     *
     * @return \DarkGhostHunter\Laralerts\Alert|\DarkGhostHunter\Laralerts\Bag
     */
    function alert(string $message = null, string ...$types): Alert|Bag
    {
        $manager = app(Bag::class);

        if (! func_num_args()) {
            return $manager;
        }

        return $manager->new()->message($message)->types(...$types);
    }
}
