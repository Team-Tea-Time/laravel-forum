# Laravel forum package

**Note: this is a fork of the excellent Laravel forum solution written by Atrakeur (https://github.com/Atrakeur/laravel-forum).** It has been heavily rewritten to provide more flexible permission handling, cleaner structure and some minor optimisations. Functionally it's very similar to the original package, but my goal is to build on it over time to suit my needs. You might find that it suits yours too, and I've

## Goals

This package aims to provide a solid basis for implementing a forum inside a Laravel application.
It focuses on taking care of all the tedious and repetitive work of building a forum (such as categories, subcategories, threads and posts), allowing you to spend more time on your website features and how the forum integrates with you application.
It provides a set of event hooks to enable adjustments to the core workflow and, additionally, provides an easy means of overriding the core classes for more advanced functionality tweaking.

This package is far from finished, and pull requests are always welcome to make this package better together.

## Installation

### Import the package

To install, simply add the following line to your composer .json and run composer update:

```json
"Eorzea/forum": "dev-master"
```

Then add the following service provider to your app.php:

```php
'Eorzea\Forum\ForumServiceProvider',
```

Update your packages:

`composer update`

### Update your database

Publish the package migrations:

`php artisan migrate:publish Eorzea/forum`

Then run your migrations:

`php artisan migrate`

Once complete, you can define your categories and sub-categories in the forum_categories table. The schema is simple, so you should be able to do that on your own using Laravel seeds.

Once your categories are set up, go to <app hostname>/forum and you should see a brand new forum.

### Deploy the controller

Run the forum install command to auto-deploy the forum controller to your app/controllers folder:
`php artisan forum:install`

### Integrate into your app

You can publish the config and migration files right into your Laravel app:
`php artisan config:publish Eorzea/forum`
`php artisan migrate:publish Eorzea/forum`

If all goes well, you should find the configuration files inside app/config/packages/Eorzea/forum and three new migrations in app/database/migrations.

Now you can create the database schema using the default Laravel command `php artisan migrate` .

Finally, run the command `php artisan forum:install` to auto-deploy the forum controller in your app/controllers folder. You can override any of the methods in this controller to alter the behaviour of the forum.

## Customisation

### Configuration

To adjust configuration (including permissions and integration options), publish the package config files:

`php artisan config:publish Eorzea/forum`

You'll find them in app/config/packages/Eorzea/forum.

### Controller methods

You can override any of the methods in the controller (app/controllers/ForumController.php by default) to adjust the behaviour of your forum.

### Views

Publish the package view files to your views folder:

`php artisan view:publish Eorzea/forum`

You can then adjust the views however you like. I suggest editing the master view to make it extend your app's main layout to easily integrate the forum with your design.

## Features

This package is currently in early development stages. However, feel free to post issues and features requests at https://github.com/Riari/laravel-forum/issues.

 * Category nesting (up to 2 levels)
 * Threads inside categories
 * Posts
 * Easy user integration (through config files and callbacks)
 * Permissions integration, with basic handling out of the box (through config files and callbacks)
 * Lightweight & blazing fast (designed with caching and high speed in mind)
 * Default markup written with [Bootstrap](http://getbootstrap.com/) in mind
