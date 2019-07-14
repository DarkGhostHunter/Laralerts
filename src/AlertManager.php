<?php

namespace DarkGhostHunter\Laralerts;

use BadMethodCallException;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Session\Store;
use Illuminate\Support\Traits\Macroable;

/**
 * Class AlertManager
 *
 * @package DarkGhostHunter\Laralerts
 *
 * @method \DarkGhostHunter\Laralerts\Alert message(string $text)
 * @method \DarkGhostHunter\Laralerts\Alert raw(string $text)
 * @method \DarkGhostHunter\Laralerts\Alert lang(string $key)
 * @method \DarkGhostHunter\Laralerts\Alert dismiss()
 * @method \DarkGhostHunter\Laralerts\Alert fixed()
 * @method \DarkGhostHunter\Laralerts\Alert primary()
 * @method \DarkGhostHunter\Laralerts\Alert secondary()
 * @method \DarkGhostHunter\Laralerts\Alert success()
 * @method \DarkGhostHunter\Laralerts\Alert danger()
 * @method \DarkGhostHunter\Laralerts\Alert warning()
 * @method \DarkGhostHunter\Laralerts\Alert info()
 * @method \DarkGhostHunter\Laralerts\Alert light()
 * @method \DarkGhostHunter\Laralerts\Alert dark()
 * @method \DarkGhostHunter\Laralerts\Alert classes(...$classes)
 */
class AlertManager
{
    use Concerns\AlertManager\HasGettersAndSetters,
        Macroable {
        __call as macroCall;
    }

    /**
     * Session Store
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * The actual Alert Bag
     *
     * @var \DarkGhostHunter\Laralerts\AlertBag
     */
    protected $alertBag;

    /**
     * Session Key to handle the Alert Bag
     *
     * @var string
     */
    protected $key;

    /**
     * Default Type of the Alerts to make
     *
     * @var string
     */
    protected $type;

    /**
     * The Default dismiss behaviour of the Alerts to make
     *
     * @var mixed
     */
    protected $dismiss;

    /**
     * Manager constructor.
     *
     * @param \DarkGhostHunter\Laralerts\AlertBag $alertBag
     * @param \Illuminate\Session\Store $session
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(AlertBag $alertBag, Store $session, Repository $config)
    {
        $this->session = $session;
        $this->alertBag = $alertBag;
        $this->key = $config->get('laralerts.key');
        $this->type = $config->get('laralerts.type');
        $this->dismiss = $config->get('laralerts.dismiss');
    }

    /**
     * Keeps the Alert Bag for another Request
     *
     * @return $this
     */
    public function withOld()
    {
        $this->alertBag->markForReflash();

        if ($this->session->isStarted()) {
            $this->session->keep($this->key);
        }

        return $this;
    }

    /**
     * Adds an Alert from a JSON string
     *
     * @param string $json
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function addFromJson(string $json)
    {
        return $this->add(Alert::fromJson($json));
    }

    /**
     * Adds an Alert and returns the same added Alert.
     *
     * @param \DarkGhostHunter\Laralerts\Alert $alert
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function add(Alert $alert)
    {
        $this->retrieveAlertBag()->add($alert);

        return $alert;
    }

    /**
     * Retrieves a new Alert Bag or the old, depending if it should be kept
     *
     * @return \DarkGhostHunter\Laralerts\AlertBag
     */
    protected function retrieveAlertBag()
    {
        // If the Alert Bag is not marked for a "reflash", then we will reuse the same alert bag
        // but flush all the alerts, keeping only one instance in the whole application. If the
        // Alert Bag is dirty (modified), then we will by pass the reflash checking altogether.
        if (!$this->alertBag->isDirty() && !$this->alertBag->shouldReflash()) {
            $this->alertBag->flush();
        }

        // Ensure the Alert Bag is in the session if it hasn't been already flashed into it.
        $this->flashBagInSession();

        return $this->alertBag;
    }

    /**
     * Flash an Alert Bag into the Session Store if it was not flashed before
     *
     * @return $this
     */
    protected function flashBagInSession()
    {
        if ($this->session->isStarted() && !$this->session->has($this->key)) {
            $this->session->flash($this->key, $this->alertBag);
        }

        return $this;
    }

    /**
     * Adds an Alert from an array
     *
     * @param array $attributes
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function addFromArray(array $attributes)
    {
        return $this->add(Alert::fromArray($attributes));
    }

    /**
     * Add many alerts from an Array. Returns the number of alerts added.
     *
     * @param array $alerts
     * @return int
     */
    public function addManyFromArray(array $alerts)
    {
        $i = 0;

        foreach ($alerts as $alert) {
            $this->addFromArray($alert);
            ++$i;
        }

        return $i;
    }

    /**
     * Makes a new Alert instance
     *
     * @param string|null $message
     * @param string|null $type
     * @param bool|null $dismiss
     * @param string|null $classes
     * @return \DarkGhostHunter\Laralerts\Alert
     */
    public function make(string $message = null, string $type = null, bool $dismiss = null, string $classes = null)
    {
        return new Alert($message, $type ?? $this->type, $dismiss ?? $this->dismiss, $classes);
    }

    /**
     * Gracefully pass the call to a new Alert instance
     *
     * @param $method
     * @param $parameters
     * @return \DarkGhostHunter\Laralerts\Alert
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (is_callable([Alert::class, $method]) || in_array($method, Alert::getTypes(), false)) {
            return $this->add($this->make())->{$method}(...$parameters);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}