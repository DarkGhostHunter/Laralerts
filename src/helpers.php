<?php

use DarkGhostHunter\Laralerts\AlertFactory;

if (! function_exists('alert')) {

    /**
     * Returns the Alert Factory.
     * If a message is passed, we'll assume you want to create an Alert
     *
     * @param string|null $message
     * @param string|null $type
     * @param bool $dismiss
     * @return \DarkGhostHunter\Laralerts\AlertFactory|\DarkGhostHunter\Laralerts\Alert
     */
    function alert(string $message = null, string $type = 'info', bool $dismiss = null) {

        /** @var AlertFactory $factory */
        $factory = app(AlertFactory::class);

        if (!$message) {
            return $factory;
        }

        $alert = $factory->message($message);

        if ($type) {
            $alert->setType($type);
        }

        if ($dismiss === true) {
            $alert->dismiss();
        } elseif($dismiss === false) {
            $alert->fixed();
        }

        return $alert;
    }

}