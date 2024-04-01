<?php

namespace TeamTeaTime\Forum\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;

class FeatureTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create dummy login route for the default redirection
        Route::get('login', function () {})->name('login');
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('forum.frontend.preset', 'blade-tailwind');
        $app['config']->set('forum.integration.user_model', User::class);
    }
}
