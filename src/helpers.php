<?php

use DarkGhostHunter\Laralerts\Alert;
use DarkGhostHunter\Laralerts\AlertManager;

if (! function_exists('alert')) {

    /**
     * Creates an Alert
     *
     * @param string|null $message
     * @param string|null $type
     * @param bool|null $dismiss
     * @return \DarkGhostHunter\Laralerts\AlertManager|\DarkGhostHunter\Laralerts\Alert
     */
    function alert(string $message = null, string $type = null, bool $dismiss = null)
    {
        $manager = app(AlertManager::class);

        if (! $message) {
            return $manager;
        }

        $alert = $manager->message($message);

        if ($type) {
            $alert->setType($type);
        }

        if ($dismiss !== null) {
            $alert->setDismiss($dismiss);
        }

        return $alert;
    }
}

if (! function_exists('alert_if')) {

    /**
     * Creates an Alert based on condition
     *
     * @param mixed $condition
     * @param string $message
     * @param string|null $type
     * @param bool|null $dismiss
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    function alert_if($condition, string $message, string $type = null, bool $dismiss = null)
    {
        return $condition ? alert($message, $type, $dismiss) : new Alert;
    }
}

if (! function_exists('alert_unless')) {
    /**
     * Creates an Alert unless the condition evaluates as false
     *
     * @param mixed $condition
     * @param string $message
     * @param string|null $type
     * @param bool|null $dismiss
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    function alert_unless($condition, string $message, string $type = null, bool $dismiss = null)
    {
        return alert_if(!$condition, $message, $type, $dismiss);
    }
}
