<?php

use DarkGhostHunter\Laralerts\AlertFactory;

if (! function_exists('alert')) {

    /**
     * Returns the Alert Factory.
     *
     * If a message is passed, we'll assume you want to create an Alert
     *
     * @param string|null $message
     * @param string|null $type
     * @return \DarkGhostHunter\Laralerts\AlertFactory|\DarkGhostHunter\Laralerts\Alert
     */
    function alert(string $message = null, string $type = 'info') {

        /** @var AlertFactory $factory */
        $factory = app(AlertFactory::class);

        if ($message) {
            $alert = $factory->add()->message($message);

            if ($type) {
                $alert->{$type}();
            }

            return $alert;
        }

        return $factory;
    }

}