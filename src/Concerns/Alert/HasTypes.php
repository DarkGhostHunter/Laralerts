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
     * Alert types and class to map into the alert HTML
     *
     * @var array
     */
    protected static $types = [
        'primary'   => 'alert-primary',
        'secondary' => 'alert-secondary',
        'success'   => 'alert-success',
        'danger'    => 'alert-danger',
        'warning'   => 'alert-warning',
        'info'      => 'alert-info',
        'light'     => 'alert-light',
        'dark'      => 'alert-dark',
    ];

    /**
     * Alert type
     *
     * @var string
     */
    protected $type;

    /**
     * Alert type class
     *
     * @var
     */
    protected $typeClass;

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
     * @param  array $types
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
     * @param  string $type
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \BadMethodCallException
     */
    public function setType(string $type)
    {
        if (! isset(static::$types[$type])) {
            throw new BadMethodCallException("The [$type] is not a valid Alert type");
        }

        $this->type = $type;

        $this->typeClass = static::$types[$type];

        return $this;
    }

    /**
     * Return the Alert type class
     *
     * @return string
     */
    public function getTypeClass()
    {
        return $this->typeClass;
    }

    /**
     * Set the Alert type class
     *
     * @param  string $typeClass
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function setTypeClass(string $typeClass)
    {
        $this->typeClass = $typeClass;

        return $this;
    }
}
