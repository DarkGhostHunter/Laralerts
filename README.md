![Jeff Sheldon - Unsplash (UL) #eOLpJytrbsQ](https://images.unsplash.com/photo-1416339306562-f3d12fefd36f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1200&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/laralerts/v/stable)](https://packagist.org/packages/darkghosthunter/laralerts) [![License](https://poser.pugx.org/darkghosthunter/laralerts/license)](https://packagist.org/packages/darkghosthunter/laralerts) ![](https://img.shields.io/packagist/php-v/darkghosthunter/laralerts.svg) ![](https://github.com/DarkGhostHunter/Lararut/workflows/PHP%20Composer/badge.svg) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Laralerts/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Laralerts?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/eba13abff3c823c00f5b/maintainability)](https://codeclimate.com/github/DarkGhostHunter/Laralerts/maintainability) [![Laravel Octane Compatible](https://img.shields.io/badge/Laravel%20Octane-Compatible-success?style=flat&logo=laravel)](https://github.com/laravel/octane) 

# Laralerts

Quickly set one or multiple Alerts in your backend, and render them in the frontend.

Laralerts is compatible with **any** frontend framework to better suit your app, not the other way around.

## Requirements

* Laravel 7.x, 8.x or later
* PHP 7.4 or later.

> For older versions support, consider helping by sponsoring or donating.

## Installation

You can install the package via composer:

```bash
composer require darkghosthunter/laralerts
```

If you don't have anything to start with in your frontend, you can use [Laravel Jetstream](https://jetstream.laravel.com/), or go the classic way and use [Bootstrap](https://getbootstrap.com), [Bulma.io](https://bulma.io/), [UI kit](https://getuikit.com/), [TailwindCSS](https://tailwindcss.com/) and [INK](http://ink.sapo.pt/). 

## Usage

Laralerts allows you to set a list of Alerts in your application and render them in the frontend.

The default Alert renderer uses Bootstrap code to transform each alert into [Bootstrap Alerts](https://getbootstrap.com/docs/5.0/components/alerts/). If you're not using Bootstrap, you can [create your own](#creating-a-custom-renderer) for your particular framework.

### Quickstart

To set an Alert in your frontend, you can use the `alert()` helper, or the `Alert` Facade. A good place to use them is before sending a View or Redirect response to the browser, like in your HTTP Controllers.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;

class ArticleController extends Controller
{
    /**
     * Update the Article 
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string'
        ]);
        
        $article->fill($request)->save();
        
        alert('Your article has been updated!', 'success');
        
        return redirect()->action('ArticleController@edit', $article);
    }
}
```

The `alert()` helper accepts the text *message* and the **types** of the alert. In the above example, we created a "success" alert.

To render them in the frontend, use the `<x-laralerts />` Blade component which will take care of the magic, anywhere you want to put it.

```blade
<div class="header">
    <h1>Welcome to my site</h1>
    <x-laralerts />
</div>
```

If there is at least one Alert to be rendered, the above will be transformed into this:

```html
<div class="header">
    <h1>Welcome to my site</h1>
    <div class="top-alerts">
        <div class="alert alert-success" role="alert">
            Your article has been updated!
        </div>
    </div>
</div>
```

### Message

Add the text inside the Alert using the `message()` method. Yeah, that's it.

```php
<?php

use DarkGhostHunter\Laralerts\Facades\Alert;

alert()->message('You are gonna love this! 😍')->types('success');

alert()->message('We will email you a copy!')->types('info');
```

```html
<div class="alert alert-success" role="alert">
    You are gonna love this! 😍
</div>

<div class="alert alert-info" role="alert">
    We will email you a copy!
</div>
```

> By default, the `message()` method escapes the text. If you want to send a raw message, you should use [`raw()`](#raw-message).

### Raw message

Since the `message()` method escapes the text for safety, you can use the `raw()` method to output the any raw string you set. This allows you to use HTML for personalized messages, like adding some _style_, links, or any other HTML.

```php
<?php

alert()->message('This is <strong>FUBAR</strong>.')->types('warning');

alert()->raw('But this is <strong>important</strong>.')->types('warning');
```

```html
<div class="alert alert-warning" role="alert">
    This is &lt;strong&gt;FUBAR&lt;/strong&gt;.
</div>

<div class="alert alert-warning" role="alert">
    But this is <strong>important</strong>.
</div>
```

**Warning: Don't use `raw()` to show user-generated content. You have been warned**.

### Alert Type

You can set an alert "type" by its name by just simply setting it with the `types()` method.

```php
<?php

alert()->message('Your message was sent!')->types('primary');

alert()->message('There is an unread message.')->types('info', 'fade');
```

```html
<div class="alert alert-primary" role="alert">
    Your message was sent!
</div>
<div class="alert alert-primary fade" role="alert">
    Your message was sent!
</div>
```

The types are just aliases for custom CSS classes and HTML, which are then translated by the Renderer to the proper code.

> You can issue more than one type. How the type gets rendered into the Alert will be the Rendered responsibility. The default Bootstrap renderer will set each unrecognized type as an additional CSS class.

### Localization

To gracefully localize messages on the fly, use the `trans()` method, which is a mirror to [the `__()` helper](https://laravel.com/docs/localization#retrieving-translation-strings).

```php
<?php

alert()->lang('email.changed', ['email' => $email], 'es')->types('success');
```

```html
<div class="alert alert-success" role="alert">
    ¡Tu email ha sido cambiado a "margarita@madrid.cl" con éxito!
</div>
```

### Dismiss

Most of frontend frameworks have alerts or notifications that can be dismissible. You can set an Alert to be dismissible using `dismiss()`.

```php
alert()->message('You can disregard this')->dismiss();
```

If you want to change your mind, you can use `dismiss(false)`:

```php
alert()->message('You can disregard this')->dismiss(false);
```

How the dismissible alert is transformed into code will depend on the renderer itself.

### Conditional Alerts

You can also push an Alert if a condition evaluates to true or false by using `when()` and `unless()`, respectively.

```php
<?php

use Illuminate\Support\Facades\Auth;

alert('You are authenticated')->when(Auth::check());

alert('You have messages in your inbox')->unless(Auth::user()->mailbox()->isEmpty());
```

### Persistent Alerts

> Persistent Alerts require [sessions enabled](https://laravel.com/docs/session). 

Alerts only last for the next response sent to the browser. To make any alert persistent you can use the `persistAs()` method with a key to identify the alert.

```php
alert()->message('Your disk size is almost full')->types('danger')->persistAs('disk.full');
```

Once you're done, you can immediately delete the persistent Alert using `abandon()` directly from the helper, with the name of the persisted Alert. It will return `true` if the persisted Alert is found, or `false` if not.

```php
alert()->abandon('disk.full');
```

> Persistent Alerts are **not idempotent**. To ensure one Persistent Alert is not duplicated, use [`unique()`](#persist-only-if-not-persisted). 

#### Persist only if not persisted

Since Persistent Alerts are no idempotent, you can use the `unique()` method to create an unique Alert to persist. If the persisted Alert didn't exist before, it will be created.

```php
alert()->unique('disk.full')->message('Your disk size is almost full')->types('danger');
```

## Configuration

Laralerts works out-of-the-box with some common defaults, but if you need a better approach for your particular application, you can configure some parameters. First, publish the configuration file.

```bash
php artisan vendor:publish --provider="DarkGhostHunter\Laralerts\LaralertsServiceProvider" --tag="config"
``` 

Let's examine the configuration array, which is quite simple:

```php
<?php 

return [
    'renderer' => 'bootstrap',
    'key' => '_alerts',
];
```

### Renderer

```php
<?php 

return [
    'renderer' => 'bootstrap',
];
```

The default renderer to use with Laralerts. This package ships with Bootstrap 5 renderer, but you can [create your own](#renderers) for other frontend frameworks like [Bulma.io](https://bulma.io/), [UI kit](https://getuikit.com/), [TailwindCSS](https://tailwindcss.com/) and [INK](http://ink.sapo.pt/), or even your own custom frontend framework.

### Session Key

The Alert Bag is registered into the Session by a given key, which is `_alerts` by default. If you're using this key name for other things in your session, you should change the key name.

```php
<?php 

return [
    'key' => '_alerts',
];
```

> For ease of mind, the Alerts serialize and unserialize as `array`, so you don't have to worry about storage concerns.

## Renderers

Alerts get rendered by a Renderer, which takes the Alert data and transforms them into an HTML string. This makes swapping a frontend framework easier, and have better flexibility when rendering HTML.

### Creating a custom renderer

You can create your own using the `Renderer` contract, and registering it into the `RendererManager` in your `AppServiceProvider`. You can use the `BootstrapRenderer` as a starting point.

```php
<?php

use DarkGhostHunter\Laralerts\RendererManager;
use App\Alerts\Renderers\BulmaRenderer;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot(RendererManager $alert)
{
    $alert->extend('bulma', function ($app) {
        return new BulmaRenderer($app);
    });
}
```

Then, in your config file, set the renderer to the one you have registered.

```php
// config/laralerts.php
return [
    'renderer' => 'bulma'
    
    // ...
];
```

When you issue an alert, the alert will be rendered using the renderer you have set.

```php
<?php

alert()->message('Popping colors!')->types('primary');
```

```html
<div class="notification is-primary">
    Popping colors!
</div> 
```

### Alerts Container HTML

When the Renderer receives Alerts to render, it will call a "container" view which will render all the Alerts by using a loop. This is hard-coded into each Renderer, so the default `BootstrapRenderer` calls the `laralerts::bootstrap.container` by default.

```html
@if($alerts)
    <div class="alerts">
        @each('laralerts::bootstrap.alert', $alerts, 'alert')
    </div>
@endif
```

You may be using another frontend framework different from Bootstrap 5, or you may want to change the HTML to better suit your application design. In any case, you can override the View files in `views/vendor/laralerts`:

* `container.blade.php`: The HTML that contains all the Alerts.
* `alert.blade.php`: The HTML for a single Alert.

The variables the `alert.blade.php` view receives are set from by Renderer. For the case of the included Bootstrap renderer, these are:

* `$alert->message`: The message to show inside the Alert.
* `$alert->classes`: The CSS classes to incorporate into the Alert.
* `$alert->dismissible`: A boolean that sets the alert as dismissible or not.

## JSON Alerts

### Receiving JSON Alerts

Sometimes your application may receive a JSON Alert from an external service using this package. You can quickly add this JSON as an Alert to your application using the `fromJson()` method.

```json
{
    "alert": {
        "message": "Email delivered",
        "types": [
            "success",
            "important"
        ],
        "dismissible": false
    }
}
```

```php
alert()->fromJson($json);
```

This will work as long the JSON **has the `message` key** with the text to include inside the Alert. Additionally, you can add the `types` and `dismiss` keys to add an Alert, with the possibility of override them afterwards.

### Sending JSON Alerts

This library has a convenient way to add Alerts into your JSON Responses. This can be very useful to add your alerts to each response being sent to the browser, like combining this package with [Laravel Jetstream](https://jetstream.laravel.com/).

Just simply [add the `laralerts.json` middleware](https://laravel.com/docs/middleware#registering-middleware) into your `api` routes or, if you're using [Laravel Jetstream](https://jetstream.laravel.com/) or similar, as a [global middleware](https://laravel.com/docs/8.x/middleware#global-middleware).

When you return a `JsonResponse` to the browser, the middleware will append the alert as JSON using the same [session key](#session-key) defined in the configuration, which is `_alerts` by default. It also accepts the `key` parameter to use as an alternative, compatible with *dot notation*. Here is an example:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::group('api', function () {
    
    Route::post('create')->uses('UserController@create');
    Route::post('update')->uses('UserController@update');
    
})->middleware('laralerts.json:_status.alerts');
```

When you receive a JSON Response, you will see the alerts appended to whichever key you issued. Using the above example, we should see the `alerts` key under the `_status` key:

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
                "types" : ["success", "important"],
                "dismiss": true
            }
        ]
    }
}
```

## Security

If you discover any security related issues, please email darkghosthunter@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
