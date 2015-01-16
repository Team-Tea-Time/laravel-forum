[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/build-status/master)

**Warning: this package is currently under heavy development and should not be considered stable. Feel free to explore, test and contribute, but don't use it for production just yet!**

# Laravel forum package

**Note: this is a fork of the excellent Laravel forum solution written by Atrakeur (https://github.com/Atrakeur/laravel-forum).** I've rewritten the permission handling and made some minor optimisations throughout, and my goal is to introduce new features over time to use it in a new project (see below for details).

## Goals

This package aims to provide a solid basis for implementing a forum in a Laravel application. It focuses on taking care of conventional forum features, allowing you to spend more time on building your application and how the forum integrates with it.

In addition to allowing controller methods to be overridden to tweak their behaviour, the package also provides extensive configuration options including permission callbacks and integration options.

## Features

This package is currently under heavy development. Feel free to post issues and features requests at https://github.com/Riari/laravel-forum/issues.

 * Categories with nesting (up to 2 levels) and weighting
 * Threads & posts
 * Pagination
 * User integration (through config files and callbacks)
 * Permissions integration, with basic defaults (through config files and callbacks)
 * Lightweight & blazing fast (designed with caching and high speed in mind)
 * Default views written with [Bootstrap](http://getbootstrap.com/) compatibility in mind

### Planned features
 * Thread pinning & locking
 * Read/unread thread status (with icons and 'new posts' page)
 * Post & thread deletion
 * Permalinks for posts

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

### Deploy the controller

Run the forum install command to auto-deploy the forum controller to your app/controllers folder:

`php artisan forum:install`

### Update your database

Publish the package migrations:

`php artisan migrate:publish Eorzea/forum`

Then run your migrations:

`php artisan migrate`

Once complete, you can define your categories and sub-categories in the forum_categories table. The schema is simple, so you should be able to do that on your own using Laravel seeds.

Once your categories are set up, go to <app hostname>/forum and you should see a brand new forum.

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
