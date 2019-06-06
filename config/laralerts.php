<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Blade Directive name
    |--------------------------------------------------------------------------
    |
    | This library registers a Blade directive called "alerts", but you may be
    | already using tha name in your application. In that case, you may want
    | to change it so the directive doesn't collide with other directives.
    |
    */

    'directive' => 'alerts',

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

    'key' => '_alerts',

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

    /*
    |--------------------------------------------------------------------------
    | Dismiss
    |--------------------------------------------------------------------------
    |
    | When creating an Alert, these are not dismissible by default. To override
    | this, set this key to "true", making every Alert dismissible by default.
    | Of course you can override this default behaviour when making an Alert.
    |
    */

    'dismiss' => false,

];

