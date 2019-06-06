<?php

use DarkGhostHunter\Laralerts\AlertFactory;

if (! function_exists('alert')) {

    /**
     * Creates an Alert
     *
     * @param string|null $message
     * @param string|null $type
     * @param bool|null $dismiss
     * @return \DarkGhostHunter\Laralerts\AlertFactory|\DarkGhostHunter\Laralerts\Alert
     */
    function alert(string $message = null, string $type = null, bool $dismiss = null)
    {
        /** @var AlertFactory $factory */
        $factory = app(AlertFactory::class);

        if (! $message) {
            return $factory;
        }

        $alert = $factory->message($message);

        if ($type) {
            $alert->setType($type);
        }

        if ($dismiss !== null) {
            $alert->setDismiss($dismiss);
        }

        return $alert;
    }
}