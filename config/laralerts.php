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

    /*
    |--------------------------------------------------------------------------
    | Default tags
    |--------------------------------------------------------------------------
    |
    | Alerts support tagging, meanining you can filter which alerts to present
    | in your frontend by a name, like "global" or "admin". This contains the
    | default tags all Alerts made in your application will have by default.
    |
    | Supported: "array", "string".
    |
    */

    'tags' => 'default',
];
