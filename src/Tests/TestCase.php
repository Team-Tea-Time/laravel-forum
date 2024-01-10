<?php

namespace TeamTeaTime\Forum\Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // Override user model in config
        config(['forum.integration.user_model' => User::class]);

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            'Kalnoy\Nestedset\NestedSetServiceProvider',
            'TeamTeaTime\Forum\ForumServiceProvider',
        ];
    }
}
