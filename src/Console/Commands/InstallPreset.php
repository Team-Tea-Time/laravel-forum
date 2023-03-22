<?php

namespace TeamTeaTime\Forum\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class InstallPreset extends Command
{
    protected $signature = 'forum:install {preset}';

    protected $description = 'Install the forum preset.';

    public function handle()
    {
        $preset = $this->argument('preset', 'bootstrap');

        (new Filesystem)->ensureDirectoryExists(resource_path('views/forum'));
        (new Filesystem)->copyDirectory(__DIR__."/../../../stubs/{$preset}/resources/views", resource_path('views'));

        (new Filesystem)->ensureDirectoryExists(resource_path('views/components/forum'));
        (new Filesystem)->copyDirectory(__DIR__."/../../../stubs/{$preset}/resources/views/components/forum", resource_path('views/components'));

        $this->info('The command was successful!');
    }
}
