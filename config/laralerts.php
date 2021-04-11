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
    | For the Alerts to work, the bag containing the alerts is registered in
    | the Session store by a identifiable key. You may want to change this
    | key for another in the case you are already using it in the store.
    |
    */

    'key' => '_alerts',
];
