# Package superseeded by [Laragear/Alerts](https://github.com/Laragear/Alerts)

---

# Laralerts

Quickly set one or multiple Alerts in your backend, and render them in the frontend.

Laralerts is compatible with **any** frontend framework to better suit your app, not the other way around.

## Requirements

* Laravel 8.x or later
* PHP 8.0 or later.

> For older versions support, consider helping by sponsoring or donating.

## Installation

You can install the package via composer:

```bash
composer require darkghosthunter/laralerts
```

If you don't have anything to start with in your frontend, you can use [Laravel Jetstream](https://jetstream.laravel.com/), or go the classic way and use [Bootstrap](https://getbootstrap.com), [Bulma.io](https://bulma.io/), [UI kit](https://getuikit.com/), [TailwindCSS](https://tailwindcss.com/) and [INK](http://ink.sapo.pt/), among many others. 

## Usage

Laralerts allows you to set a list of Alerts in your application and render them in the frontend in just a few minutes.

The default Alert renderer uses Bootstrap code to transform each alert into [Bootstrap Alerts](https://getbootstrap.com/docs/5.0/components/alerts/). If you're not using Bootstrap, you can [create your own](#creating-a-custom-renderer) for your particular framework.

### Quickstart

To set an Alert in your frontend, you can use the `alert()` helper, or the `Alert` Facade. A good place to use them is before sending a response to the browser, like in your HTTP Controllers.

If you're sending a redirect, Laralerts will automatically flash the alert so the next request can show it. 

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

If there is at least one Alert to be rendered, the above will be transformed into proper HTML:

```html
<div class="header">
    <h1>Welcome to my site</h1>
    <div class="alerts">
        <div class="alert alert-success" role="alert">
            Your article has been updated!
        </div>
    </div>
</div>
```

### Message

Add the text inside the Alert using the `message()` method. That's it.

```php
<?php

use DarkGhostHunter\Laralerts\Facades\Alert;

alert()->message('You are gonna love this! 😍')->types('success');

alert()->message('We will email you 📨 a copy!')->types('info');
```

```html
<div class="alert alert-success" role="alert">
    You are gonna love this! 😍
</div>

<div class="alert alert-info" role="alert">
    We will email you 📨 a copy!
</div>
```

> By default, the `message()` method escapes the text. If you want to send a raw message, you should use [`raw()`](#raw-message).

### Raw message

Since the `message()` method escapes the text for safety, you can use the `raw()` method to output a string verbatim. This allows you to use HTML for personalized messages, like adding some _style_, links, or even scripts.

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

**Warning: Don't use `raw()` to show user-generated content. YOU HAVE BEEN WARNED**.

### Alert Type

You can set an alert "type" by its name by just setting it with the `types()` method. It also accepts multiple types.

```php
<?php

alert()->message('Your message was sent!')->types('primary');

alert()->message('There is an unread message.')->types('info', 'fade');
```

```html
<div class="alert alert-primary" role="alert">
    Your message was sent!
</div>
<div class="alert alert-info fade" role="alert">
    There is an unread message.
</div>
```

The types are just aliases for custom CSS classes and HTML, which are then translated by the Renderer to the proper code.

> The Renderer receives the list of types and changes them into CSS classes accordingly. The default Bootstrap Renderer will set each unrecognized type as an additional CSS class.

### Localization

To gracefully localize messages on the fly, use the `trans()` method, which is a mirror of [the `__()` helper](https://laravel.com/docs/localization#retrieving-translation-strings).

```php
<?php

alert()->trans('email.changed', ['email' => $email], 'es')->types('success');
```

```html
<div class="alert alert-success" role="alert">
    ¡Tu email ha sido cambiado a "margarita@madrid.cl" con éxito!
</div>
```

You can also use `transChoice()` with the same parameters of [`trans_choice()`](https://laravel.com/docs/localization#pluralization).

```php
<?php

alert()->transChoice('messages.apples', 1)->types('success');
alert()->transChoice('messages.apples', 10)->types('success');
```

```html
<div class="alert alert-success" role="alert">
    ¡Ahora tienes 1 manzana! 
</div>

<div class="alert alert-success" role="alert">
    ¡Ahora tienes 10 manzanas! 
</div>
```

### Dismiss

Most of the frontend frameworks have alerts or notifications that can be dismissible, but require adding more than a single class to allow for interactivity. You can set an Alert to be dismissible using `dismiss()`.

```php
alert()->message('You can disregard this')->type('success')->dismiss();
```

If you want to change your mind, you can use `dismiss(false)`:

```php
alert()->message('You can disregard this')->type('success')->dismiss(false);
```

How the dismissible alert is transformed into code will depend on the renderer itself. The default Bootstrap renderer adds the proper CSS classes and a dismiss button automatically.

```html
<div class="alert alert-success alert-dismissible fade show" role="alert">
  You can disregard this
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
```

### Conditional Alerts

You can also push an Alert if a condition evaluates to true or false by using `when()` and `unless()`, respectively. Further method calls will be sent to the void.

```php
<?php

use Illuminate\Support\Facades\Auth;

alert()->when(Auth::check())
    ->message('You are authenticated')
    ->types('success');

alert()->unless(Auth::user()->mailbox()->isNotEmpty())
       ->message('You have messages in your inbox')
       ->types('warning');
```

### Persistent Alerts

Alerts only last for the actual request being sent. On redirects, these are [flashed into the session](https://laravel.com/docs/8.x/session#flash-data) so these are available on the next request (the redirection target).

To make any alert persistent you can use the `persistAs()` method with a key to identify the alert.

```php
alert()->message('Your disk size is almost full')->types('danger')->persistAs('disk.full');
```

> Setting a persistent alert replaces any previous set with the same key. 

Once you're done, you can delete the persistent Alert using `abandon()` directly from the helper using the key of the persisted Alert. It will return `true` if the persisted Alert is found, or `false` if not. For example, we can abandon the previous alert if the disk is no longer full.

```php
if ($disk->notFull()) {
    alert()->abandon('disk.full');
}
```

### Links

Setting up links for an alert doesn't have to be cumbersome. You can easily replace a string between curly braces in your message for a link using `to()`, `route()`, `action()`, and `away()`.

```php
<?php

alert()->message('Remember, you can follow your order in your {dashboard}.')
    ->types('success')
    ->to('dashboard', '/dashboard/orders')
```

Links can also work over translated messages, as long these have a word in curly braces.

```php
<?php
// You can see your package status in the {tracking}.

alert()->trans('user.dashboard.tracking.order', ['order' => $order->tracking_number])
    ->types('success')
    ->route('tracking', 'orders.tracking', ['order' => 45])
```

If you have more than one link, you can chain multiple links to a message.

```php
<?php

alert()->trans('Your {product} is contained in this {order}.')
    ->types('success')
    ->action('product', [\App\Http\Controllers\Product::class, 'show'], ['product' => 180])
    ->to('order', '/dashboard/order/45')
```

> Links strings are case-sensitive, and replaces all occurrences of the same string. You can [create your own Renderer](#creating-a-custom-renderer) if this is not desired. 

### Tags

Sometimes you may have more than one place in your site to place Alerts, like one for global alerts and other for small user alerts. Tags can work to filter which Alerts you want to render.

You can set the tags of the Alert using `tag()`. 

```php
alert()->message('Maintenance is scheduled for tomorrow')
    ->type('warning')
    ->tag('user', 'admin')
```

Using the [Laralerts directive](#quickstart), you can filter the Alerts to render by the tag names using the `:tags` slot.

```blade
<!-- Render the Alerts in the default list -->
<x-laralerts :tags="'default'" />

<!-- Here we will render alerts for users and admins. -->
<x-laralerts :tags="['user', 'admin']" />
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
    'tags' => 'default',
];
```

### Renderer

```php
<?php 

return [
    'renderer' => 'bootstrap',
];
```

This picks the Renderer to use when transforming Alerts into HTML.

This package ships with Bootstrap 5 renderer, but you can [create your own](#renderers) for other frontend frameworks like [Bulma.io](https://bulma.io/), [UI kit](https://getuikit.com/), [TailwindCSS](https://tailwindcss.com/) and [INK](http://ink.sapo.pt/), or even your own custom frontend framework.

### Session Key

```php
<?php 

return [
    'key' => '_alerts',
];
```

When alerts are flashed or persisted, these are stored in the Session by a given key, which is `_alerts` by default. If you're using this key name for other things, you may want to change it.

This key is also used when [sending JSON alerts](#sending-json-alerts).

### Default tag list

```php
<?php 

return [
    'tags' => ['user', 'admin'],
];
```

This holds the default tag list to inject to all Alerts when created. You can leave this alone if you're not using [tags](#tags).

## Renderers

A Renderer takes a [collection](https://laravel.com/docs/collections) of Alerts and transforms each into an HTML string. This makes swapping a frontend framework easier, and allows greater flexibility when rendering HTML.

### Creating a custom renderer

You can create your own using the `Renderer` contract, and registering it into the `RendererManager` in your `AppServiceProvider`. You can use the `BootstrapRenderer` as a starting point.

```php
<?php

use DarkGhostHunter\Laralerts\RendererManager;
use App\Alerts\Renderers\TailwindRenderer;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot(RendererManager $alert)
{
    $alert->extend('tailwind', function ($app) {
        return new TailwindRenderer($app['blade.compiler']);
    });
}
```

Then, in your config file, set the renderer to the one you have registered.

```php
// config/laralerts.php

return [
    'renderer' => 'tailwind'
    
    // ...
];
```

When you issue an alert, the alert will be rendered using your own custom renderer.

```php
<?php

alert()->message('Popping colors!')->types('primary');
```

```html
<div class="notification type-primary">
    Popping colors!
</div> 
```

### Alerts Container HTML

When the Renderer receives Alerts to render, it will call a "container" view which will render all the Alerts by using a loop.

For example, the included `BootstrapRenderer` calls the `laralerts::bootstrap.container`.

```html
@if($alerts->isNotEmpty())
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

As you're suspecting, you can publish the views and override them to suit your needs.

    php artisan vendor:publish --provider="DarkGhostHunter\Laralerts\LaralertsServiceProvider" --tag="views"

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

> If your key is already present in the JSON response, Laralerts **won't overwrite the key value**. Ensure the key is never present in the response.

## Testing

To test if alerts were generated, you can use `Alert::fake()`, which works like any other faked services. It returns a fake Alert Bag that holds a copy of all alerts generated, which exposes some convenient assertion methods.

```php
use \DarkGhostHunter\Laralerts\Facades\Alert;

public function test_alert_sent()
{
    $alerts = Alert::fake();
    
    $this->post('/comment', ['body' => 'cool'])->assertOk();
    
    $alerts->assertHasOne();
}
```

The following assertions are available:

| Method                    | Description                                                            |
|---------------------------|------------------------------------------------------------------------|
| `assertEmpty()`           | Check if the alert bag doesn't contains alerts.                        |
| `assertNotEmpty()`        | Check if the alert bag contains any alert.                             |
| `assertHasOne()`          | Check if the alert bag contains only one alert.                        |
| `assertHas($count)`       | Check if the alert bag contains the exact amount of alerts.            |
| `assertHasPersistent()`   | Check if the alert bag contains at least one persistent alert.         |
| `assertHasNoPersistent()` | Check if the alert bag doesn't contains a persistent alert.            |
| `assertPersistentCount()` | Check if the alert bag contains the exact amount of persistent alerts. |

### Asserting specific alerts

The fake Alert bag allows building conditions for the existence (or nonexistence) of alerts with specific properties, by using `assertAlert()`. 

Once you build your conditions, you can use `exists()` to check if any alert matches, or `missing()` to check if no alert should match.

```php
$bag->assertAlert()->withMessage('Hello world!')->exists();

$bag->assertAlert()->withTypes('danger')->dismissible()->missing();
```

Alternatively, you can use `count()` if you expect a specific number of alerts to match the given conditions, or `unique()` for matching only one alert.

```php
$bag->assertAlert()->persisted()->count(2);

$bag->assertAlert()->notDismissible()->withTag('toast')->unique();
```

The following conditions are available:

| Method              | Description                                       |
|---------------------|---------------------------------------------------|
| `withRaw()`         | Find alerts with the given raw message.           |
| `withMessage()`     | Find alerts with the given message.               |
| `withTrans()`       | Find alerts with the translated message.          |
| `withTransChoice()` | Find alerts with the translated (choice) message. |
| `withAway()`        | Find alerts with a link away.                     |
| `withTo()`          | Find alerts with a link to a path.                |
| `withRoute()`       | Find alerts with a link to a route.               |
| `withAction()`      | Find alerts with a link to a controller action.   |
| `withTypes()`       | Find alerts with exactly the given types.         |
| `persisted()`       | Find alerts persisted.                            |
| `notPersisted()`    | Find alerts not persisted.                        |
| `persistedAs()`     | Find alerts persisted with the issued keys.       |
| `dismissible()`     | Find alerts dismissible.                          |
| `notDismissible()`  | Find alerts not dismissible.                      |
| `withTag()`         | Find alerts with all the given tags.              |
| `withAnyTag()`      | Find alerts with any of the given tags.           |

## Security

If you discover any security related issues, please email darkghosthunter@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
