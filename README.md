![Jeff Sheldon - Unsplash (UL) #eOLpJytrbsQ](https://images.unsplash.com/photo-1416339306562-f3d12fefd36f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1200&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/laralerts/v/stable)](https://packagist.org/packages/darkghosthunter/laralerts) [![License](https://poser.pugx.org/darkghosthunter/laralerts/license)](https://packagist.org/packages/darkghosthunter/laralerts)
![](https://img.shields.io/packagist/php-v/darkghosthunter/laralerts.svg)
 [![Build Status](https://travis-ci.com/DarkGhostHunter/Laralerts.svg?branch=master)](https://travis-ci.com/DarkGhostHunter/Laralerts) [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Laralerts/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Laralerts?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/eba13abff3c823c00f5b/maintainability)](https://codeclimate.com/github/DarkGhostHunter/Laralerts/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/eba13abff3c823c00f5b/test_coverage)](https://codeclimate.com/github/DarkGhostHunter/Laralerts/test_coverage)

# Laralerts

Quickly set one or multiple Alerts (Bootstrap 4) in your backend, and render them in the frontend.

## Installation

You can install the package via composer:

```bash
composer require darkghosthunter/laralerts
```

Additionally, [download Bootstrap 4](https://getbootstrap.com) for your frontend.

## Basic Usage

To set an Alert in your frontend, you can use the `alert()` helper, or the `Alert` Facade. For example, when you are using your controllers. 

```php
<?php

namespace App\Http\Controllers;

use DarkGhostHunter\Laralerts\Facades\Alert;

class ExampleController extends Controller
{
    public function send()
    {
        alert('Your message was sent!', 'success');
            
        Alert::message('We will also email you a copy!')
            ->info()
            ->dismissible();
        
        return view('page');
    }
}
```

The `alert()` helper accepts the message text and alert type, quickly making your alerts into one-liners.

To render them in the frontend, use the `@alerts` Blade directive which will take care of the rest.

```blade
<div class="header">
    <h1>Welcome</h1>
    <div class="alerts">
        @alerts
    </div>
</div>
```

### Message

This allows you to add a text string into the Alert. You can use a simple text string, or an HTML text.

```php
<?php

alert()->message('Your message was sent!')
    ->success();
    
Alert::message('We will email you a copy!')
    ->info()
    ->dismissable();
```

> **Warning:** the message is **NOT** escaped. You're fully responsible of what the string contains.

#### Using Localization

Instead of witting a raw message, you can use the `lang()` method, which is a mirror to [the `@lang` Blade directive](https://laravel.com/docs/localization#retrieving-translation-strings), to localize the message on the fly.

```php
<?php

alert()->lang('email.sent')->success();
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

> By default, the Alert type is not set in your Alert, so they will be transparent, but you can [set a default](#defaults).

### Dismissible

To make an Alert dismissible, use the `dismissable()` method. 

```php
<?php

alert()->message('Your message was sent!')
    ->success()
    ->dismissible();

```

It also accepts as first parameter a boolean to enable or disable the `show` class (visible), and optionally a class (or classes) to use for animation, like with [animate.css](https://daneden.github.io/animate.css/).

By default, if it's dismissible, it will use the `fade` class for animation.

```php
<?php

alert()->message('Ups, the recipient did not reply!')
    ->warning()
    ->dismissible(false, 'animated bounceOutLeft');
```

When using a dismissible Alert, the default dismissible class will be added, along with the button to dismiss at the end.

### Additional Classes

You can issue additional classes to your Alert seamlessly using the `classes()` method, which accepts a list of classes to be added to the Alert.


```php
<?php

alert()->message('Your message was sent!')
    ->success()
    ->classes('message-sent', 'global-alert');
```

### Defaults

Additionally, you can ease your developer life by setting defaults for the Alerts being made in your application.

For this to work, set the defaults inside the `boot()` method of your `AppServiceProvider`. 

```php
<?php

namespace App\Providers;

use DarkGhostHunter\Laralerts\Facades\Alert;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Alert::setDefaultType('info')
            ->setDefaultDismissible(true)
            ->setDefaultClasses('font-weight-bold', 'text-center');
    }

}
```

If you are concerned with performance, you can use the `resolving()` method of the Service Container to set the defaults only when resolving the `AlertFactory` from it.

```php
<?php

$this->app->resolving(AlertFactory::class, function ($factory) {
    $factory->setDefaultType('info')
        ->setDefaultDismissible(true)
        ->setDefaultClasses('font-weight-bold', 'text-center');
});
```

#### Default Type

The default type of the Alerts in the Application. You can use any of the method names.

```php
<?php

use DarkGhostHunter\Laralerts\Facades\Alert;

Alert::setType('secondary');
```

#### Default Dismissible

If the alerts should be dismissible by default. When not, they will be fixed. 

```php
<?php

use DarkGhostHunter\Laralerts\Facades\Alert;

Alert::setDefaultDismissible(true);
```

#### Default Classes

You can add default classes to the Alert HTML tag. You can use this for custom entrance animation classes, or other styling. These will be always appended to the tag.

```php
<?php

use DarkGhostHunter\Laralerts\Facades\Alert;

Alert::setDefaultClasses('text-center', 'font-weight-bold');
```

#### Session Key

By default, this package puts the `AlertBag` into the `alerts` key of the Session. If you're using it for other purposes, it may be overwritten. To avoid this, you can change the default key for anything you want so it doesn't collide with other keys.

```php
<?php

use DarkGhostHunter\Laralerts\Facades\Alert;

Alert::setKey('Laralerts');
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email darkghosthunter@gmail.com instead of using the issue tracker.

## Credits

- [Italo Israel Baeza Cabrera](https://github.com/darkghosthunter)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.