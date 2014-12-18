# Laravel forum package

**Note: this is a fork of the excellent Laravel forum solution written by Atrakeur (https://github.com/Atrakeur/laravel-forum).** My intention is to strip out the user rights handling and replace it with Zizaco Entrust integration, as well as alter other aspects of the package to suit my needs. You might find that it suits yours too, so I'm being careful to retain the flexibility of the config and view publishing seen in the original package.

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

### Integrate into your app

Before anything, in some cases (L4) you may need to run an update for composer before these next steps, so:

```php
composer update
```

Now publish forum's files right into your Laravel app:
`php artisan config:publish Eorzea/forum`
`php artisan migrate:publish Eorzea/forum`

If all goes well, you should find the configuration files inside app/config/packages/Eorzea/forum and three new migrations in app/database/migrations.

Now you can create the database schema using the default Laravel command `php artisan migrate` .

To enable you to fully customise your forum, the package is integrated inside your application using two application level controllers.
Run the command `php artisan forum:install` to auto-deploy the controllers in your app/controllers folder. (Please note that the command will fail if a file with the same name already exists.)

### Customise

To tweak the views, publish them to your views folder using the Laravel command:

`php artisan view:publish Eorzea/forum`

The very last step needed is to create some categories and subcategories in the forum_categories tables. The schema is very basic and you should be able to do that on your own using Laravel seeds.

Once your categories are set up, go to <app hostname>/forum and you should see a brand new forum.

More information on how to integrate it with your login system is available through the config files comments. By default, it should run well with Laravel's default auth mechanism (which is also extended by certain auth packages such as Zizaco Confide, so they will inherently be compatible with this package).

## Features

This package is currently in early development stages. However, feel free to post issues and features requests at https://github.com/Riari/laravel-forum/issues.

 * Category nesting (up to 2 levels)
 * Threads inside categories
 * Posts (with hooks for app integration)
 * Easy user integration (through config files and callbacks)
 * Zizaco Entrust permission integration (through config files and callbacks)
 * Lightweight & blazing fast (designed with caching and high speed in mind)
 * Default markup written for [Bootstrap](http://getbootstrap.com/)

## Event Hooks

This package provides event hooks to enable you to alter its behaviour. Below is a complete list of these hooks, the parameters they take and when they're executed.

| Events               | Params        | Conditions                            |
| -------------        |:-------------:| ---------------------------------------------:                     |
| forum.create.thread      | $thread        | Called during thread creation. Can be used to modify thread contents.     |
| forum.create.post    | $post      | Called during post creation. Can be used to modify post contents. |
| forum.modified.thread    | $thread        | Called after saving a thread. Can be used for logging purposes.          |
| forum.modified.post  | $post      | Called after saving a post. Can be used for logging purposes.        |
