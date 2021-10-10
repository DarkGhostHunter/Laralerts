<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Renderer
    |--------------------------------------------------------------------------
    |
    | When an Alert is rendered into HTML, it uses a "render" which transforms
    | the Alert into HTML code for a given frontend framework. By default, it
    | uses "Bootstrap 5", but you can change it or create your own renderer.
    |
    */

    'renderer' => 'bootstrap',

    /*
    |--------------------------------------------------------------------------
    | Session key
    |--------------------------------------------------------------------------
    |
    | For the Alerts to work, the bag containing them is registered inside the
    | Session store by an identifiable key. You may want to change this key
    | for any other in case it collides with a key you're already using.
    |
    */

    'key' => '_alerts',
];
