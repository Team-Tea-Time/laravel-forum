[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Riari/laravel-forum/build-status/master)

**Warning: this package is currently under heavy development and should not be considered stable. Feel free to explore, test and contribute, but don't use it for production just yet!**

# Laravel forum package

**Note: this is a fork of the excellent Laravel forum solution written by Atrakeur (https://github.com/Atrakeur/laravel-forum).** Although I've largely rewritten and adapted it to facilitate the kind of forum I want in a new project, the original package was a crucial starting point and I wouldn't have been able to do any of this without it - so credit goes first and foremost to Atrakeur for his hard work.

Among the many things I've done in this version of the package are the revised permission system, moderation tools and some minor optimisations throughout. My goal is to introduce new features over time as needed for an upcoming project. See further down for a list of current and planned features.

## Goals

This package aims to provide a solid basis for implementing a forum in a Laravel application. It focuses on taking care of conventional forum features, allowing you to spend more time building your application and the way in which the forum integrates with it.

In addition to an overridable main controller, the package also provides extensive configuration options including permission callbacks, integration settings and preferences to tune the behaviour of your forum.

## Features

This package is currently under heavy development. Feel free to post issues and features requests at https://github.com/Riari/laravel-forum/issues.

 * Categories with nesting (up to 2 levels) and weighting
 * Threads with locking, pinning and deletion
 * Posts with permalinks, editing and deletion
 * Optional soft-deletion of threads and posts
 * Pagination
 * Basic auth & user integration out of the box
 * Permissions integration with basic defaults:
   * Category access
   * Create threads
   * Delete threads
   * Lock threads
   * Pin threads
   * Reply to threads
   * Edit posts
   * Delete posts
 * Lightweight & blazing fast (designed with caching and high speed in mind)
 * Default views written with [Bootstrap](http://getbootstrap.com/) compatibility in mind
 * Multilingual (English and French translations available out of the box)

### Planned features
 * Read/unread thread status (with icons and 'new posts' page)

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

## Important notes

### Regarding permissions

The default permission callbacks don't allow users to perform certain actions such as deleting threads or posts; you'll need to modify them to return TRUE based on your own criteria. For example, if you use [Zizaco/entrust](https://github.com/Zizaco/entrust), you might change your `delete_threads` callback to return `Entrust::can('forum_threads_delete');`, allowing users with a role that grants the `forum_threads_delete` permission to delete threads.

Note that the default set of views include links for deleting, editing and replying, and their visibility is controlled by the permission callbacks. 

### Regarding thread and post deletion

There's no confirmation step implemented by default for thread and post deletion - if a user clicks 'Delete [thread/post]' and has permission to perform that action, it'll happen instantantly. You can implement a confirmation step in a variety of ways, either by modifying the links to lead to an intermediary view in your app prompting the user to confirm, or using JS-based dialogs (or a combination of both).
