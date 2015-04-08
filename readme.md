[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/build-status/master)

**See the [wiki](https://github.com/Riari/laravel-forum/wiki) for an overview of this project and its goals, features and customisation options.**

## Installation

**Important!** Before you proceed, please be aware that user functionality (signup, login, user profiles, etc) is not provided by this package. It only interfaces with your user model and authentication facade (Laravel's built-in one by default) to associate users with their threads/posts. If you need user handling, I recommend these packages:

 * [Zizaco/confide](https://github.com/Zizaco/confide) - for account creation, login, logout, confirmation by e-mail, password reset, etc.
 * [Zizaco/entrust](https://github.com/Zizaco/entrust) - for role-based user permissions.


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
