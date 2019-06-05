<?php

namespace DarkGhostHunter\Laralerts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Support\HtmlString;
use JsonSerializable;

class Alert implements Arrayable, Jsonable, JsonSerializable, Htmlable
{
    use Concerns\HasClasses,
        Concerns\HasDismissible;

    /**
     * Accepted types of alert
     *
     * @const array
     */
    public const TYPES = [
        'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark',
    ];

    /**
     * HTML message
     *
     * @var string
     */
    protected $message;

    /**
     * View Factory
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Alert constructor.
     *
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
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
     * Encodes and sets the message of the Alert
     *
     * @param string $text
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function escape(string $text)
    {
        return $this->message(e($text));
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
            'dismiss' => $this->dismiss,
            'classes' => $this->parseTagClasses(),
        ];
    }

    /**
     * Parses the whole class for the Alert
     *
     * @return string
     */
    protected function parseTagClasses()
    {
        return implode(' ', $this->classes);
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
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return new HtmlString(
            $this->view->make(
                $this->dismiss ? 'laralerts::alert-dismiss' : 'laralerts::alert'
            )->with($this->toArray())->render()
        );
    }
}