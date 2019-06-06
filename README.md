![Jeff Sheldon - Unsplash (UL) #eOLpJytrbsQ](https://images.unsplash.com/photo-1416339306562-f3d12fefd36f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1200&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/laralerts/v/stable)](https://packagist.org/packages/darkghosthunter/laralerts) [![License](https://poser.pugx.org/darkghosthunter/laralerts/license)](https://packagist.org/packages/darkghosthunter/laralerts)
![](https://img.shields.io/packagist/php-v/darkghosthunter/laralerts.svg)
 [![Build Status](https://travis-ci.com/DarkGhostHunter/Laralerts.svg?branch=master)](https://travis-ci.com/DarkGhostHunter/Laralerts) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Laralerts/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Laralerts?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/eba13abff3c823c00f5b/maintainability)](https://codeclimate.com/github/DarkGhostHunter/Laralerts/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/eba13abff3c823c00f5b/test_coverage)](https://codeclimate.com/github/DarkGhostHunter/Laralerts/test_coverage)

# Laralerts

Quickly set one or multiple Alerts in your backend, and render them in the frontend.

This version 2.0 is compatible with any frontend framework, and allows you to modify the rendered HTML.

## Installation

You can install the package via composer:

```bash
composer require darkghosthunter/laralerts
```

Additionally, [download Bootstrap 4](https://getbootstrap.com) for your frontend if you don't have anything. 

And that's it. Everything works out of the box.

## Basic Usage

To set an Alert in your frontend, you can use the `alert()` helper, or the `Alert` Facade. A good place to use them is before sending a Response to the browser, like in your Controllers.

```php
<?php

namespace App\Http\Controllers;

use DarkGhostHunter\Laralerts\Facades\Alert;

class ExampleController extends Controller
{
    /**
     * Tell the User the message was sent 
     * 
     * @return \Illuminate\Http\Response
     */
    public function sent()
    {
        alert('Your message was sent!', 'success', true);

        Alert::message('We will also email you a copy!')
            ->info()
            ->dismiss();

        return response()->view('page');
    }
}
```

The `alert()` helper accepts the text *message*, alert *type*, and if it should be *dismissible*, expressively making your alerts into identifiable one-liners.

To render them in the frontend, use the `@alerts` Blade directive which will take care of the magic.

```blade
<div class="header">
    <h1>Welcome to my site</h1>
    @alerts
</div>
```

And if there is no Alerts to show, don't worry, nothing will be rendered.

### Message

Add the text inside the Alert using the `message()` method. Yeah, that's it.

```php
<?php

use DarkGhostHunter\Laralerts\Facades\Alert;

alert()->message('Your message was sent!')
    ->success();
    
Alert::message('We will email you a copy!')
    ->info();
```

```html
<div class="alert alert-success" role="alert">
    Your message was sent!
</div>

<div class="alert alert-info" role="alert">
    <strong>We will email you a copy!</strong>
</div>
```

> By default, the `message()` method escapes the text. If you want to send a raw message, you should use `raw()`.

#### Raw message

Since the `message()` method escape the text for safety, you can use the `raw()` method to do the same with the untouched string. This allows you to use HTML for more personalized messages, like adding some style. 

```php
<?php

alert()->raw('<strong>This is very important</strong>')
    ->warning();
```

```html
<div class="alert warning" role="alert">
    <strong>This is very important</strong>
</div>
```

> Advice: Don't use `raw()` when you need to show **user-generated content**.

#### Using Localization

To gracefully localize messages on the fly, use the `lang()` method, which is a mirror to [the `@lang` Blade directive](https://laravel.com/docs/localization#retrieving-translation-strings).

```php
<?php

alert()->lang('email.sent')->success();
```

```html
<div class="alert alert-success">
    Your email has been sent succesfully!
</div>
```

### Alert Type

You can use multiple fluent methods that mirror the alert class from Bootstrap 4:

| Method | Class |
| --- | --- |
| `primary()` | `alert-primary` |
| `secondary()` | `alert-secondary` |
| `success()` | `alert-success` |
| `danger()` | `alert-danger` |
| `warning()` | `alert-warning` |
| `info()` | `alert-info` |
| `light()` | `alert-light` |
| `dark()` | `alert-dark` |

```php
<?php

alert()->message('Your message was sent!')
    ->primary();
```

```html
<div class="alert alert-primary" role="alert">
    Your message was sent!
</div>
```

> By default, the Alert type is not set in your Alert, so they will be *transparent*. Don't worry, you can easily [set a default](#type).

### Dismiss

To make an Alert dismissible, use the `dismiss()` method. This will change the HTML to make the alert dismissible. 
Using the `fixed()` method will make the Alert non-dismissible.

```php
<?php

alert()->message('Your message was sent!')
    ->success()
    ->dismiss();
```

```html
<div class="alert alert-info alert-dismissible fade show" role="alert">
    Your message was sent!
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
```

### Additional Classes

You can issue additional classes to your Alert seamlessly using the `classes()` method, which accepts a list of classes to be added to the Alert.

```php
<?php

alert()->message('Your message was sent!')
    ->success()
    ->classes('message-sent', 'global-alert');
```

```html
<div class="alert alert-success message-sent global-alert" role="alert">
    Your message was sent!
</div>
```

### Adding Alerts to a JSON Response

This library has a convenient way to add Alerts into your JSON Responses. Just simply add the `AppendAlertsToJsonResponse` middleware into your routes or `app/Http/Kernel`, [as the documentation says](https://laravel.com/docs/middleware#registering-middleware). If you ask me, the `alerts` is a very straightforward middleware alias to use.

When you return a `JsonResponse` to the browser, the middleware will append the Alerts as JSON using the same Session Key defined in the configuration, which is `_alerts` by default, but it also accepts the `key` parameter to use as an alternative, compatible with *dot notation*. Here is the lazy way to do it as example:

```php
<?php

use Illuminate\Support\Facades\Route;
use DarkGhostHunter\Laralerts\Http\Middleware\AppendAlertsToJsonResponse;

Route::group('api', function () {
    
    Route::post('create')->uses('UserController@create');
    Route::post('update')->uses('UserController@update');
    
})->middleware(AppendAlertsToJsonResponse::class.':_status.alerts');

```

When you receive a JSON Response, you will see something the Alerts appended to whichever key you used. Using the above example, we should see the `alerts` array under the `_status` key: 

```json
{
    "resource": "users",
    "url": "/v1/users",
    "_status": {
        "timestamp":  "2019-06-05T03:47:24Z",
        "action" : "created",
        "id": 648,
        "alerts": [
            {
                "message": "The user has been created!",
                "type" : "success",
                "dismiss": true,
                "classes": null
            }
        ]
    },
}
```

To keep good performance, the Alerts will be injected into the Session only if it has started. Since the `api` routes are stateless, there is no need to worry about disabling the Session in these routes.

### Configuration

Laralerts works out-of-the-box with some common defaults, but if you need a better approach, you can set configure some parameters. First, publish the configuration file.

```bash
php artisan vendor:publish --provider="DarkGhostHunter\Laralerts\LaralertsServiceProvider"
``` 

Let's examine the configuration array:

```php
<?php 

return [
    'directive' => 'alerts',
    'key' => '_alerts',
    'type' => null,
];
```

#### Directive

This library registers the `@alerts` blade component, that has the container where all the Alerts will be rendered. If you're using the same namespace, you may want to change it so it doesn't collide with other Blade directives, like using the safe `@laralerts`.

```php
<?php 

return [
    'directive' => 'laralerts',
];
```

#### Session Key

The Alert Bag is registered into the Session by a given key, called `_alerts`. If you're using this key name for other things, you should change for other key, since if it collides the Alert Bag won't be flashed into the session.

```php
<?php 

return [
    'session_key' => '_alerts',
];
```

> For your ease of mind, the Alerts serialize and unserialize as `array`, so you don't have to worry about storage concerns. In prior versions, the whole Alert Bag was included, which added a lot of overhead.

#### Type

The default type of the Alerts in the Application. You can use any of the [included type names](#alert-type), like `success` or `info`. You can override the type anytime when you create an Alert manually.

```php
<?php 

return [
    'type' => 'primary',
];
```

#### Modifying the Types 
 
If you need to modify the Alert types, you can use the static method `Alert::setTypes()` with an array of accepted types of Alerts. You can do this on the boot method or register method of your `AppServiceProvider`.

```php
<?php

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    // ...
    
    \DarkGhostHunter\Laralerts\Alert::setTypes(['info', 'success', 'warning', 'danger', 'my-custom-type']);
}
```

### Modifying the HTML

You may be using another frontend framework different from Bootstrap 4, or you may want to change the HTML to better suit your application design. In any case, you can override the View files in `views/vendor/laralerts`:

* `alert.blade.php`: The HTML for a single Alert.
* `alert-dismiss.blade.php`: Same as above, but for a dismissible Alert.
* `alerts.blade.php`: The HTML that contains all the Alerts

The Alert view receives:

* `$message`: The message to show inside the Alert.
* `$type`: The type of Alert.  
* `$classes`: The classes to add to the HTML tag.

You can change the HTML to whatever you want, like adapting the Alert to be used with [Bulma.io Notifications](https://bulma.io/documentation/elements/notification/).

`/resources/views/vendor/laralerts/alert-dismiss.blde.php`

```blade
<div class="notification is-{{ $type }} {{ $classes }}">
    <button class="delete"></button>
    {!! $message !!}
</div>
```

#### Adding an Alert from JSON

Sometimes your application may receive a JSON Alert from an external service. You can quickly add this JSON as an Alert to your application using the `addFromJson()` method.

```php
<?php

$json = '"{"message":"Email delivered"}"';

alert()->addFromJson($json)->success()->dismiss();
```

This will work as long the JSON **has the `message` key** with the text to include inside the Alert. Additionally, you can add the `type`, `dismiss` and `classes` keys to add an Alert, with the possibility of override them afterwards. 

### Security

If you discover any security related issues, please email darkghosthunter@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.