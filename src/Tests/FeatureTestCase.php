<?php

namespace TeamTeaTime\Forum\Tests;

use Illuminate\Support\Facades\Route;

class FeatureTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create dummy login route for the default redirection
        Route::get('login', ['as' => 'login'], function () {
            return '';
        });
    }
}
