<?php

namespace DarkGhostHunter\Laralerts\Concerns\Alert;

use BadMethodCallException;

/**
 * Trait HasTypes
 *
 * @package DarkGhostHunter\Laralerts\Concerns
 *
 * @method \DarkGhostHunter\Laralerts\Alert primary()
 * @method \DarkGhostHunter\Laralerts\Alert secondary()
 * @method \DarkGhostHunter\Laralerts\Alert success()
 * @method \DarkGhostHunter\Laralerts\Alert danger()
 * @method \DarkGhostHunter\Laralerts\Alert warning()
 * @method \DarkGhostHunter\Laralerts\Alert info()
 * @method \DarkGhostHunter\Laralerts\Alert light()
 * @method \DarkGhostHunter\Laralerts\Alert dark()
 */
trait HasTypes
{
    /**
     * Accepted types of alert
     *
     * @var array
     */
    protected static $types = [
        'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark',
    ];

    /**
     * Alert type
     *
     * @var string
     */
    protected $type;

    /**
     * Return the available types for the Alert
     *
     * @return mixed
     */
    public static function getTypes()
    {
        return static::$types;
    }

    /**
     * Set the available types for the Alert
     *
     * @param array $types
     */
    public static function setTypes(array $types)
    {
        static::$types = $types;
    }

    /**
     * Append types for the Alert
     *
     * @param  array $types
     */
    public static function addTypes(array $types)
    {
        static::$types = array_merge(static::$types, $types);
    }

    /**
     * Return the Alert type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the Type for this Alert
     *
     * @param string $type
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \BadMethodCallException
     */
    public function setType(string $type)
    {
        if (! in_array($type, static::$types, false)) {
            throw new BadMethodCallException("The [$type] is not a valid Alert type");
        }

        $this->type = $type;

        return $this;
    }
}
