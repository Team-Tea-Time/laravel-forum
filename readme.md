[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=3.0)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=3.0)

**Complete documentation is available on [teamteatime.net](https://teamteatime.net/docs/laravel-forum/3.x/introduction.md).**

## Requirements

+ PHP 5.4 or above
+ Laravel 5.1 or above (5.3 or above for package version ~4.0)

## Installation

### Step 1: Install the package

Install the package via composer:

```
composer require riari/laravel-forum:~3.0
```

If you're using Laravel 5.3 or above, you'll need version ~4.0:

```
composer require riari/laravel-forum:~4.0
```

Then add the service provider to your `config/app.php`:

```php
Riari\Forum\ForumServiceProvider::class,
```

Installing the [standard front-end](https://github.com/Riari/laravel-forum-frontend) is recommended:

```
composer require riari/laravel-forum-frontend:~1.0
```

```php
Riari\Forum\Frontend\ForumFrontendServiceProvider::class,
```

### Step 2: Publish the package files

Run the vendor:publish command to publish the package config, translations and migrations to your app's directories:

`php artisan vendor:publish`

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

### Additional steps

#### Server configuration

If you installed Xdebug before version 2.3, you may need to modify `xdebug.max_nesting_level`. The suggested and new default value from version 2.3 onwards is `256`.

#### Configuration

Several configuration files are published to your application's config directory, each prefixed with `forum.`. Refer to these for a variety of options for changing the behaviour of the forum and how it integrates with key parts of your application code.

> You may need to modify the `forum.integration.user_name` config option according to your user model. This specifies which attribute on the user model should be used as a display name in the forum views.

#### Translations

Laravel Forum currently supports 11 languages: German, English, Spanish, French, Italian, Romanian, Russian, Turkish, Serbian, Portuguese (Brazil) and Swedish. The translation files are published to `resources/lang/vendor/forum/{locale}`. **Please be aware that much of the translation work in 3.0 has been done using Google Translate and probably isn't accurate. Pull requests are welcome to rectify this.**

#### Policies (user permissions)

Permissions in 3.0 are handled via the [Authorization features](http://laravel.com/docs/5.1/authorization) introduced in Laravel 5.1.11. Refer to [src/Policies](https://github.com/Riari/laravel-forum/tree/3.0/src/Policies) for a full list of policies. To override methods in a policy, just create a class extension for it somewhere in your application and change the corresponding namespace specified in the `forum.integration.policies` config array. **You'll likely want to do this for the ForumPolicy and CategoryPolicy as a minimum to prevent your regular users from managing categories and threads!**
