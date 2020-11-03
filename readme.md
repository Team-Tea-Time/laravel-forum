[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Riari/laravel-forum/badges/quality-score.png?b=5.0)](https://scrutinizer-ci.com/g/Riari/laravel-forum/?branch=5.0)

**Complete documentation is available on [teamteatime.net](https://teamteatime.net/docs/laravel-forum/5.x/).**

## Installation

Requires Laravel 6+ and PHP 7.2+.

### Step 1: Install the package

Install the package via composer:

```
composer require teamteatime/laravel-forum:~5.0
```

Then add the service provider to your `config/app.php`:

```php
TeamTeaTime\Forum\ForumServiceProvider::class,
```

### Step 2: Publish the package files

Run the vendor:publish command to publish the package config, translations and migrations to your app's directories:

`php artisan vendor:publish`

### Step 3: Update your database

Run your migrations:

`php artisan migrate`

### Additional steps

#### Server configuration

If you installed Xdebug before version 2.3, you may need to modify `xdebug.max_nesting_level`. The suggested and new default value from version 2.3 onwards is `256`.

#### Configuration

Several configuration files are published to your application's config directory, each prefixed with `forum.`. Refer to these for a variety of options for changing the behaviour of the forum and how it integrates with key parts of your application code.

> You may need to modify the `forum.integration.user_name` config option according to your user model. This specifies which attribute on the user model should be used as a display name in the forum views.

#### Translations

Laravel Forum currently supports 11 languages: German, English, Spanish, French, Italian, Romanian, Russian, Turkish, Serbian, Portuguese (Brazil) and Swedish. The translation files are published to `resources/lang/vendor/forum/{locale}`. **Some new language strings have been introduced in 5.0 but not yet translated; PRs to translate these would be greatly appreciated.**

#### Policies (user permissions)

Permissions in 3.0 are handled via the [Authorization features](http://laravel.com/docs/5.1/authorization) introduced in Laravel 5.1.11. Refer to [src/Policies](https://github.com/Riari/laravel-forum/tree/3.0/src/Policies) for a full list of policies. To override methods in a policy, just create a class extension for it somewhere in your application and change the corresponding namespace specified in the `forum.integration.policies` config array. **You'll likely want to do this for the ForumPolicy and CategoryPolicy as a minimum to prevent your regular users from managing categories and threads!**

## Development

### Tests

A GitHub Actions workflow is set up to automatically run tests via Docker for new commits and PRs. For details on how to use the images locally, see below.

#### Local execution

**Setup**

Build the MySQL and PHPUnit images:

```bash
docker build -t mysql:latest -f docker/mysql/Dockerfile .
docker build -t phpunit:latest -f docker/phpunit/Dockerfile .
```

Create a network to share between the MySQL and PHPUnit containers:

```bash
docker network create -d bridge lf-tests
```

Start the MySQL service:

```bash
docker run -d --name lf-tests-mysql --network lf-tests --mount type=tmpfs,destination=/var/lib/mysql mysql:latest
```

Install Composer dependencies:

```bash
docker run -v "$(pwd):/app" composer:2.0.3 install
```

**Execution**

```bash
docker run -v "$(pwd):/app" --network lf-tests phpunit:latest
```