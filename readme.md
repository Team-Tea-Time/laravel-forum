[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=l5-refactor)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=l5-refactor)

**This is a rewrite of the Laravel 5 (2.x.x) version and will be released as 3.0.0 in the near future. Please don't attempt to use it as it's likely to be completely broken while I work on refactoring. Thanks!**

**See the [wiki](https://github.com/Riari/laravel-forum/wiki) for an overview of this project, goals and features, as well as documentation.**

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

> In your published config, you may need to modify the `forum.integration.user_name_attribute` config option according to your user model. This specifies which attribute on the user model should be used as a display name in the forum views.

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

Once complete, you can define your categories and sub-categories in the forum_categories table. The schema is simple, so you should be able to do that on your own using Laravel seeds or straightforward SQL.

Once your categories are set up, go to <app hostname>/forum and you should see a brand new forum.
