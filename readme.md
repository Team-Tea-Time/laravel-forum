[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=l5-refactor)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=l5-refactor)

**This is a pre-release of version 3, which introduces many new features and improvements over version 2. Some parts of it may be broken and it's not well-optimised yet, but feel free to explore it. Full documentation will be published on [teamteatime.net](http://teamteatime.net/) prior to the first stable release.**

**Please refer to the [laravel-5](https://github.com/Riari/laravel-forum/tree/laravel-5) and [laravel-4](https://github.com/Riari/laravel-forum/tree/laravel-4) branches for current stable versions.**

## Installation

### Step 1: Install the package

Install the package via composer:

```
composer require riari/laravel-forum:~3.0
```

Then add the following service provider to your `config/app.php`:

```php
'Riari\Forum\Providers\ForumServiceProvider',
```

### Step 2: Publish the package files

Run the vendor:publish command to copy the controller, config, resources and migrations to your app's directories:

`php artisan vendor:publish`

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

### Additional steps

Once the package is installed, provided you are logged in, you can visit <your domain>/forum and start defining your category hierarchy using the "Create category" and "Category actions" panels:

![Category management example](http://i.imgur.com/h8DXHj1.png)

#### Configuration

Several configuration files are published to your application's config directory, each prefixed with `forum.`. Refer to these for a variety of options for changing the behaviour of the forum and how it integrates with key parts of your application code.

> You may need to modify the `forum.integration.user_name` config option according to your user model. This specifies which attribute on the user model should be used as a display name in the forum views.

#### Views

Views are published to `resources/views/vendor/forum`. The simplest way to integrate the forum with your existing design is to edit the **master** view, remove undesired markup and make it extend your application's main layout with `@extends`. Note that the master view does pull in jQuery and Bootstrap 3 by default, and includes some jQuery-based JavaScript to support some of the forum frontend features. You may wish to move it elsewhere or re-write it in your own way.

#### Translations

Laravel Forum currently supports 8 languages: German, English, Spanish, French, Italian, Romanian, Russian and Swedish. The translation files are published to `resources/lang/vendor/forum/{locale}`. **Please be aware that much of the translation work in 3.0 has been done using Google Translate and probably isn't accurate. Pull requests are welcome to rectify this.**

#### Events

The package includes a variety of [events](http://laravel.com/docs/5.1/events) for user interactions such as viewing threads. Refer to [src/Events](https://github.com/Riari/laravel-forum/tree/3.0/src/Events) for a full list.

#### Policies (user permissions)

Permissions in 3.0 are handled via the [Authorization features](http://laravel.com/docs/5.1/authorization) introduced in Laravel 5.1.11. Refer to [src/Policies](https://github.com/Riari/laravel-forum/tree/3.0/src/Policies) for a full list of policies. To override methods in a policy, just create a class extension for it somewhere in your application and change the corresponding namespace specified in the `forum.integration.policies` config array. **You'll likely want to do this for the ForumPolicy and CategoryPolicy as a minimum to prevent your regular users from managing categories and threads!** 
