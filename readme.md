# Laravel forum package

Note: this package is currently in a very alpha stage. I'm currently working on integrating it inside my own website. The base functionnality should work but I'll complete the features when requested or when I need them.

[![Build Status](https://travis-ci.org/atrakeur/laravel-forum.svg?branch=master)](https://travis-ci.org/atrakeur/laravel-forum)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/atrakeur/laravel-forum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/atrakeur/laravel-forum/?branch=master)

## Goals

This package aims to provide a good starting point implementing a forum inside a Laravel application.
It focus on taking care of all the tedious and repetiting work of forum creation (categories, subcategories, topics, messages). Allowing you to spend more time on you website features and how the forum integrates with you application.
This package will provide multiple hooks such as specials events and custom closures to allow you to customise his behavior. Additionnaly, you'll be able to extends forum's core classes to implement you own methods directly inside the core.

This package is far from finished, and pull requests are always welcome to make this package better together.

## Installation

### Import the package

To install, simply add the following line to your composer .json and run composer update:

```json
"atrakeur/forum": "dev-master"
```

Then add the following service provider to your app.php:

```php
'Atrakeur\Forum\ForumServiceProvider',
```

### Integrate into your app

Before anything, in some cases (L4) you may need to run an update for composer before these next steps, so:

```php
composer update
```
and, just to be safe, run:

```php
composer dump-autoload
```

Now publish forum's files right into your Laravel app:
`php artisan config:publish atrakeur/forum`
`php artisan migrate:publish atrakeur/forum`

If all goes well, you should find configuration files inside app/config/packages/atrakeur/forum and three new migrations in app/database/migrations.

Now you can create the database schema using the default Laravel command `php artisan migrate` .

To enable you to fully customise the package to your website, the package is integrated inside your application using two application level controllers.
Run the command `php artisan forum:install` to auto-deploy the controllers in your app/controllers folder. (Please note that if a file with the same name allready exist, the command above will fail before overriding your files.)

### Customise

To tweak the views publish them to your views folder using the Laravel command:

`php artisan view:publish atrakeur/forum`

The very last step needed is to create some categories and subcategories into the forum_categories tables. The schema is very basic and you should be able to do that on your own using Laravel seeds (TODO: give some examples)

Now you are ready to go, just load http://localhost/forum and you should see a brand new forum.

More information on how to integrate it with your login system is available through the config files comments. (TODO: give some examples) By default, it should run well on Laravel default auth.

## Features

This package is currently in (very-)alpha stage, so all of the following features may or may not work yet. However, feel free to post issues and features requests at https://github.com/atrakeur/laravel-forum/issues . I'll try to fix and improve the package as fast as I can based on your help!

 * Category nesting on 2 levels
 * Topic and messages inside categories
 * Easy user integration (through config files and callbacks)
 * Easy user right integration (through config files and callbacks)
 * Message posting (with hooks for app integration)
 * Light weight & blasing fast (designed with caching and high speed in mind)
 * Designed on bootstrap (clean and simple markup, no messy css and should integrate directly into your website)

## Events

This package provides various events as hooks to enable you to implement you own functionnality on top of forum's functionnality.
Here is a complete list of all events, as to when they are fired. When a parameter is given, you can use this parameter to change a forum's iternal object to fit your needs.

| Events               | Params        | Usage                            |
| -------------        |:-------------:| ---------------------------------------------:                     |
| forum.new.topic      | $topic        | Called before topic save. Can be used to modify topic contents     |
| forum.new.message    | $message      | Called before message save. Can be used to modify message contents |
| forum.saved.topic    | $topic        | Called after topic save. Can be used for logging purposes          |
| forum.saved.message  | $message      | Called after message save. Can be used for logging purposes        |
