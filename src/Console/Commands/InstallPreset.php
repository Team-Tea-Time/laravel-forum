<?php

namespace TeamTeaTime\Forum\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallPreset extends Command
{
    protected $signature = 'forum:install {preset}';

    protected $description = 'Install a UI preset for the forum.';

    public function handle(Filesystem $filesystem)
    {
        $preset = $this->argument('preset', 'bootstrap');

        $filesystem->ensureDirectoryExists(resource_path('views/forum'));
        $filesystem->copyDirectory(__DIR__."/../../../stubs/{$preset}/resources/views", resource_path('views'));

        $filesystem->ensureDirectoryExists(resource_path('views/components/forum'));
        $filesystem->copyDirectory(__DIR__."/../../../stubs/{$preset}/resources/views/components/forum", resource_path('views/components'));

        $this->info('The command was successful!');
    }
}
