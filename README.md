![Jeff Sheldon - Unsplash (UL) #eOLpJytrbsQ](https://images.unsplash.com/photo-1416339306562-f3d12fefd36f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1200&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/laralerts/v/stable)](https://packagist.org/packages/darkghosthunter/laralerts) [![License](https://poser.pugx.org/darkghosthunter/laralerts/license)](https://packagist.org/packages/darkghosthunter/laralerts)
![](https://img.shields.io/packagist/php-v/darkghosthunter/laralerts.svg)
 [![Build Status](https://travis-ci.com/DarkGhostHunter/Laralerts.svg?branch=master)](https://travis-ci.com/DarkGhostHunter/Laralerts) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Laralerts/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Laralerts?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/eba13abff3c823c00f5b/maintainability)](https://codeclimate.com/github/DarkGhostHunter/Laralerts/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/eba13abff3c823c00f5b/test_coverage)](https://codeclimate.com/github/DarkGhostHunter/Laralerts/test_coverage)

# Laralerts

Quickly set one or multiple Alerts in your backend, and render them in the frontend.

Laralerts is compatible with any frontend framework, and allows you to modify the rendered HTML.

## Installation

You can install the package via composer:

```bash
composer require darkghosthunter/laralerts
```

You can quick start your frontend scaffolding installing [Laravel UI](https://laravel.com/docs/6.x/frontend), specially if you don't have anything to start with.

Good alternatives are [Bulma.io](https://bulma.io/), [Materialize](https://materializecss.com/), [Semantic UI](https://semantic-ui.com/), [Material UI](https://material-ui.com), [UI kit](https://getuikit.com/) and [INK](http://ink.sapo.pt/).

And that's it. Everything works out of the box.

## Usage

### Creating an Alert

To set an Alert in your frontend, you can use the `alert()` helper, or the `Alert` Facade. A good place to use them is before sending a Response or Redirect to the browser, like in your HTTP Controllers.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;

class ArticleController extends Controller
{
    /**
     * Show the edit form for the Article 
     * 
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view('alert.edit')->with('article', $article);
    }

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
        
        alert('Your article has been updated!', 'success', true);
        
        return redirect()->action('ArticleController@edit', $article);
    }
    
    // ...
}
```

The `alert()` helper accepts the text *message*, the *type* of the alert, and if it should be *dismissible*, making your alerts into expressive one-liners.

To render them in the frontend, use the `@alerts` Blade directive which will take care of the magic, anywhere you want.

```blade
<div class="header">
    <h1>Welcome to my site</h1>
    @alerts
</div>
```

And if there is no Alerts to show, don't worry, no HTML will be rendered.

#### Conditional Alerts

You can also push an Alert if a condition evaluates to true or false. Just use the `alert_if` and `alert_unless`, respectively.

```php
<?php

alert_if(true, 'You should see this alert');
alert_unless(false, 'And this too since the condition is false!');

alert_if(false, 'No problem')->message('with chaining methods!');
```

When `alert_if` and `alert_unless` evaluate to false, you will still receive an Alert instance, but this won't be persisted into the session or response, avoiding unwanted errors in your backend.

#### Message

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
    We will email you a copy!
</div>
```

> By default, the `message()` method escapes the text. If you want to send a raw message, you should use [`raw()`](#raw-message).

#### Raw message

Since the `message()` method escape the text for safety, you can use the `raw()` method to do the same with the untouched string. This allows you to use HTML for more personalized messages, like adding some _style_, or even add JavaScript if you like to ride the wild side.

```php
<?php

alert()->message('This is <strong>FUBAR</strong>.')
    ->warning();

alert()->raw('But this is <strong>important</strong>.')
    ->warning();
```

```html
<div class="alert alert-warning" role="alert">
    This is &lt;strong&gt;FUBAR&lt;/strong&gt;.
</div>

<div class="alert alert-warning" role="alert">
    But this is <strong>important</strong>.
</div>
```

> **Warning: Don't use `raw()` to show user-generated content. You have been warned**.

#### Using Localization

To gracefully localize messages on the fly, use the `lang()` method, which is a mirror to [the `@lang` Blade directive](https://laravel.com/docs/localization#retrieving-translation-strings).

```php
<?php

alert()->lang('email.sent', ['email' => $email], 'es')->success();
```

```html
<div class="alert alert-success" role="alert">>
    ¡Tu email ha sido cambiado a "margarita@madrid.cl" con éxito!
</div>
```

#### Alert Type

You can use multiple fluent methods that mirror the Alert class from Bootstrap 4:

| Method       | Class              |
| ---         | ---              |
| `primary()`  | `alert-primary`    |
| `secondary()`| `alert-secondary`  |
| `success()`  | `alert-success`    |
| `danger()`   | `alert-danger`     |
| `warning()`  | `alert-warning`    |
| `info()`     | `alert-info`       |
| `light()`    | `alert-light`      |
| `dark()`     | `alert-dark`       |

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

> By default, Alerts don't have any default type, so when they're rendered they will be *transparent* (without style or color). Don't worry, you can easily [set a default](#default-type).

##### Adding your own fluid classes

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
    
    \DarkGhostHunter\Laralerts\Alert::setTypes(['gradient', 'popping']);
}
```

```php
<?php

alert()->message('Popping colors!')
    ->popping();
```

```html
<div class="alert alert-popping" role="alert">
    Popping colors!
</div> 
```

The array you supply is not added, but replaced entirely. If you want to just add some types, use the `addTypes()` static method.

```php
<?php

\DarkGhostHunter\Laralerts\Alert::addTypes(['these', 'are', 'added']);
```

#### Dismiss

To make an Alert dismissible, use the `dismiss()` method. This will change the Blade view used to render the Alert in HTML to make it dismissible.

By contrast, if you have set dismissible Alerts by default, using the `fixed()` method will make a particular Alert non-dismissible by using the default Blade view. 

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

> By using two different Blade views for dismissable and non-dismissable alerts, there is more flexibility on how to render them.

#### Additional Classes

You can issue additional classes to your Alert by seamlessly using the `classes()` method, which accepts a list of classes to be added into the HTML code.

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

### Persisting the Alerts

By default, in every request lifecycle (except on Redirects) you will start with an empty Alert Bag.

You can _rescue_ the old Alerts using the `withOld()` method, as long you haven't issued a new Alert before. 

Once you create a new alert, it will be appended to the existing bag of alerts from the request or redirect made before. 

```php
<?php

alert()
    ->withOld()
    ->message('Be sure to check the other alerts.')
    ->warning();
``` 

```html
<div class="alert alert-success message-sent global-alert" role="alert">
    Your message was sent!
</div>

<div class="alert alert-warning" role="alert">
    Be sure to check the other alerts.
</div>
```

### Adding Alerts to a JSON Response

This library has a convenient way to add Alerts into your JSON Responses. Just simply add the `AppendAlertsToJsonResponse` middleware into your routes or `app/Http/Kernel`, [as the documentation says](https://laravel.com/docs/middleware#registering-middleware). If you ask me, the `alerts` is a very straightforward middleware alias to use.

When you return a `JsonResponse` to the browser, the middleware will append the alert as JSON using the same Session Key defined in the configuration, which is `_alerts` by default. It also accepts the `key` parameter to use as an alternative, compatible with *dot notation*. Here is the lazy way to do it as example:

```php
<?php

use Illuminate\Support\Facades\Route;
use DarkGhostHunter\Laralerts\Http\Middleware\AppendAlertsToJsonResponse;

Route::group('api', function () {
    
    Route::post('create')->uses('UserController@create');
    Route::post('update')->uses('UserController@update');
    
})->middleware(AppendAlertsToJsonResponse::class.':_status.alerts');
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
                "type" : "success",
                "dismiss": true,
                "classes": null
            }
        ]
    }
}
```

To keep good performance, the Alerts will be injected into the Session only if it has started. The `api` routes are stateless, and here the Sessions are not used.

## Configuration

Laralerts works out-of-the-box with some common defaults, but if you need a better approach for your particular application, you can configure some parameters. First, publish the configuration file.

```bash
php artisan vendor:publish --provider="DarkGhostHunter\Laralerts\LaralertsServiceProvider"
``` 

Let's examine the configuration array, which is quite simple:

```php
<?php 

return [
    'directive' => 'alerts',
    'key' => '_alerts',
    'type' => null,
];
```

### Directive

This library registers the `@alerts` blade component, that has the container where all the Alerts will be rendered. 

If you're using a directive with the same name, you may want to change it so it doesn't collide. I totally recommend you to use `@laralerts` as a safe bet.

```php
<?php 

return [
    'directive' => 'laralerts',
];
```

> When changing this parameter, you may want to use `php artisan view:clear` to rebuild your views with the new directive.

### Session Key

The Alert Bag is registered into the Session by a given key, which is `_alerts` by default. If you're using this key name for other things, you should change the key name.

```php
<?php 

return [
    'session_key' => '_alerts',
];
```

> For your ease of mind, the Alerts serialize and unserialize as `array`, so you don't have to worry about storage concerns.

### Default Type

The default type of the Alerts in the Application. You can use any of the [included type names](#alert-type), like `success` or `info`. You can override the default type anytime when you create an Alert manually.

```php
<?php 

return [
    'type' => 'primary',
];
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

Sometimes your application may receive a JSON Alert from an external service using this package. You can quickly add this JSON as an Alert to your application using the `addFromJson()` method.

```php
<?php

$json = '"{"message":"Email delivered"}"';

alert()->addFromJson($json)->success()->dismiss();
```

This will work as long the JSON **has the `message` key** with the text to include inside the Alert. Additionally, you can add the `type`, `dismiss` and `classes` keys to add an Alert, with the possibility of override them afterwards.

If you need to add many alerts from a JSON string, use the `addManyFromJson()` method and (optionally) issue the key where they are using dot notation.

When doing this, the alerts will be created using the `message`, `type`, `dismiss` and `classes` respectively.

```php
<?php

$json = '"{"content": {"alerts": {"message":"Email delivered"} } }"';

alert()->addManyFromJson($json, 'content.alerts');
```

### Macros

This package is totally compatible with Macros. You can add your own macros to the `AlertManager` class, which has access to the Session Store and the Alert Bag. 

You can add your own macros the usual way, preferably directly through the class itself.

```php
<?php

use DarkGhostHunter\Laralerts\AlertManager;

AlertManager::macro('countAlerts', function () {
    return $this->alertBag->count();
});
```

## Security

If you discover any security related issues, please email darkghosthunter@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
