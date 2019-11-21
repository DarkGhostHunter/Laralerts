<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Blade Directive name
    |--------------------------------------------------------------------------
    |
    | This library registers a Blade directive called "alerts", but you may be
    | already using the name in your application. In that case, you may want
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
    | key for another in the case you are already using it in the store.
    |
    */

    'key' => '_alerts',

    /*
    |--------------------------------------------------------------------------
    | Type
    |--------------------------------------------------------------------------
    |
    | All Alerts are created "in blank state", without any alert type defined.
    | Setting a default type will allow you to skip issuing the type in each
    | alert in your app. Anyway, this can be overridden inside each alert.
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
