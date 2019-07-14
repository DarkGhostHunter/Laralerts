<?php

namespace DarkGhostHunter\Laralerts\Concerns;

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
        return self::$types;
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

    /**
     * If the call was made to a type, set that type or bail out.
     *
     * @param $name
     * @param $arguments
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, self::$types, false)) {
            return $this->setType($name);
        }

        throw new BadMethodCallException("Method $name does not exist.");
    }
}