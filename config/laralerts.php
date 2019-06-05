<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Blade component name
    |--------------------------------------------------------------------------
    |
    | This library registers a Blade component called "alerts", but you may be
    | already using tha name in your application. In that case, you may want
    | to change it for anything else it doesn't collide your other aliases.
    |
    */

    'component' => 'alerts',

    /*
    |--------------------------------------------------------------------------
    | Session key
    |--------------------------------------------------------------------------
    |
    | For the Alerts to work, the bag containing the alerts is registered in
    | the Session store by a identifiable key. You may want to change this
    | key for another in the case you're already using it in the session.
    |
    */

    'session_key' => '_alerts',

    /*
    |--------------------------------------------------------------------------
    | Type
    |--------------------------------------------------------------------------
    |
    | All Alerts are created "in blank state", without any alert type. Setting
    | a default type will allow you to skip setting them individually in your
    | application, and still be able to override it on each one of the these.
    |
    */

    'type' => null,

];

