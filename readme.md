![Tests](https://github.com/Team-Tea-Time/laravel-forum/actions/workflows/tests.yml/badge.svg) ![phpcs](https://github.com/Team-Tea-Time/laravel-forum/actions/workflows/phpcs.yml/badge.svg) ![Packagist Downloads](https://img.shields.io/packagist/dm/riari/laravel-forum) ![Packagist License](https://img.shields.io/packagist/l/riari/laravel-forum)

![Laravel Forum Logo](./logo.png)

**Complete documentation is available on [teamteatime.net](https://www.teamteatime.net/).**

## Versions

| **Laravel version** | **Livewire version \*** | **Package version** | **PHP version** |
|---------------------|-------------------------|---------------------|-----------------|
| 13                  | 4                       | ^8.0                | ^8.3            |
| 12                  | 3                       | ^7.0                | ^8.2            |

_* Only applicable if you intend to use a Livewire-based UI preset._

Please note that older package versions not listed above are no longer maintained or supported.

See the [support policy in the Laravel docs](https://laravel.com/docs/13.x/releases#support-policy) for more information about Laravel release versions, their supported PHP versions, and how long they receive bug & security fixes.

## Installation

### Step 1: Install the package

Install the package via composer:

```
composer require riari/laravel-forum:^8.0
```

[Package Discovery](https://laravel.com/docs/13.x/packages#package-discovery) should take care of registering the service provider automatically, but if you need to do so manually, add the service provider to your `bootstrap/providers.php`:

```php
TeamTeaTime\Forum\ForumServiceProvider::class,
```

### Step 2: Publish the package files

Run the vendor:publish command to publish the package config, translations and migrations to your app's directories:

`php artisan vendor:publish`

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

### Step 4: Install a UI preset

A `forum:preset-install {name}` command is available for installing UI presets. Run `forum:preset-list` to see a list of available presets. You must install one of these to publish the corresponding views to your application. For example:

`php artisan forum:preset-install livewire-tailwind`

> [!NOTE]  
> By default, the `livewire-tailwind` preset is set as the active one in the `forum.frontend.preset` config value. This preset requires Livewire and a few other dependencies. Refer to [UI Presets](https://www.teamteatime.net/docs/laravel-forum/8.x/front-end/ui-presets) for details.

### Additional steps

#### Configuration

Several configuration files are published to `config/forum`. Refer to these for a variety of options for changing the behaviour of the forum and how it integrates with key parts of your application code.

> [!NOTE]  
> You may need to modify the `forum.integration.user_name` config option according to your user model. This specifies which attribute on the user model should be used as a display name in the forum views.

#### Translations

Laravel Forum currently supports 15 languages: German, English, Spanish, French, Italian, Dutch, Romanian, Russian, Thai, Turkish, Serbian, Portuguese (Brazil), Swedish, Chinese, and Indonesian. The translation files are published to `resources/lang/vendor/forum/{locale}`.

> [!NOTE]  
> Some new language strings have been introduced in version 6 but not yet translated; PRs to translate these would be greatly appreciated.

## Development

If you wish to contribute, an easy way to set up the package for local development is [Team-Tea-Time/laravel-studio](https://github.com/Team-Tea-Time/laravel-studio), which is set up to load a local working copy of this repository (see the [readme](https://github.com/Team-Tea-Time/laravel-studio/blob/13.x/readme.md#usage) for usage details).

### Running tests

Bring up the MySQL service:

```bash
docker-compose up -d mysql
```

Install Composer dependencies:

```bash
docker-compose run --rm composer install
```

Run the phpunit container to execute tests:

```bash
docker-compose run --rm phpunit
```

### Seeding

The package tables can be seeded with sample categories, threads, posts, and a user via `forum:seed`:

```bash
docker-compose exec php-fpm php artisan forum:seed
```
