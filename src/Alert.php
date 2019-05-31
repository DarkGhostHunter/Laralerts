<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class Alert implements Arrayable, Jsonable, JsonSerializable, Htmlable
{
    use Concerns\HasClasses,
        Concerns\HasDismissible;

    /**
     * Default class to use as base to the alert
     *
     * @const string
     */
    protected const ALERT_CLASS = 'alert';

    /**
     * Accepted types of alert
     *
     * @const array
     */
    public const TYPES = [
        'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark',
    ];

    /**
     * The HTML string to use as dismissible button
     *
     * @var string
     */
    protected static $closeHtml = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

    /**
     * Type of Alert (class)
     *
     * @var string
     */
    protected $type;

    /**
     * Additional classes to add to the HTML tag
     *
     * @var string
     */
    protected $classes;

    /**
     * HTML message
     *
     * @var string
     */
    protected $message;

    /**
     * If the Alert should be dismissible
     *
     * @var bool
     */
    protected $dismissible = false;

    /**
     * If the dismissible Alert should start displayed using the 'show' class
     *
     * @var boolean
     */
    protected $show = true;

    /**
     * The animation class for dismissal
     *
     * @var string
     */
    protected $animationClass = 'fade';

    /**
     * Return the HTML to use as Close button
     *
     * @return string
     */
    public static function getCloseHtml()
    {
        return self::$closeHtml;
    }

    /**
     * Set the HTML to use as Close button
     *
     * @param string $closeHtml
     */
    public static function setCloseHtml(string $closeHtml)
    {
        self::$closeHtml = $closeHtml;
    }

    /**
     * Return the Alert message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Localizes the message of the Alert
     *
     * @param string $key
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function lang(string $key)
    {
        return $this->message(__($key));
    }

    /**
     * Sets the message of the Alert
     *
     * @param string $text
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function message(string $text)
    {
        $this->message = $text;

        return $this;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'dismissible' => $this->dismissible,
        ];
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return '<div class="' . $this->parseTagClasses() . '" role="alert">'
            . $this->message . ($this->dismissible ? self::$closeHtml : '')
            . '</div>';
    }

    /**
     * Transforms this Alert as an HTML string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * Parses the whole class for the Alert
     *
     * @return string
     */
    protected function parseTagClasses()
    {
        return implode(' ', array_filter([
            self::ALERT_CLASS,
            $this->type ? self::ALERT_CLASS .'-'. $this->type : '',
            $this->parseDismissClasses(),
            $this->classes,
        ]));
    }

    /**
     * Return the classes if the alert is dismissible
     *
     * @return string|void
     */
    protected function parseDismissClasses()
    {
        if ($this->dismissible) {
            return implode(' ', array_filter([
                self::ALERT_CLASS . '-dismissible',
                $this->animationClass,
                $this->show ? 'show' : null,
            ]));
        }
    }
}