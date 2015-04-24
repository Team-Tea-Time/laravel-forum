[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/build-status/master)

**See the [wiki](https://github.com/Riari/laravel-forum/wiki) for an overview of this project, goals and features, as well as documentation.**

**Note: the Laravel 4 version only receives bug fixes and important amendments to existing features. Only the Laravel 5 version currently receives new features.**

## Installation

### Step 1: Install the package

Install the package via composer:

```
composer require riari/laravel-forum:~1.0
```

Then add the following service provider to your app/config/app.php:

```php
'Riari\Forum\ForumServiceProvider',
```

### Step 2: Deploy the controller

Run the forum install command to auto-deploy the forum controller to your app/controllers folder:

`php artisan forum:install`

### Step 3: Update your database

Publish the package migrations:

`php artisan migrate:publish riari/laravel-forum`

Then run your migrations:

`php artisan migrate`

Once complete, you can define your categories and sub-categories in the forum_categories table. The schema is simple, so you should be able to do that on your own using Laravel seeds or straightforward SQL.

Once your categories are set up, go to <app hostname>/forum and you should see a brand new forum.
