[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/build-status/master)

**See the [wiki](https://github.com/Riari/laravel-forum/wiki) for an overview of this project, goals and features, as well as documentation.**

## Installation

### Step 1: Install the package

Install the package via composer:

```
composer require riari/laravel-forum:~2.0
```

Then add the following service provider to your `config/app.php`:

```php
'Riari\Forum\ForumServiceProvider',
```

### Step 2: Publish the package files

Run the vendor:publish command to copy the controller, config, resources and migrations to your app's directories:

`php artisan vendor:publish`

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

Once complete, you can define your categories and sub-categories in the forum_categories table. The schema is simple, so you should be able to do that on your own using Laravel seeds or straightforward SQL.

Once your categories are set up, go to <app hostname>/forum and you should see a brand new forum.
