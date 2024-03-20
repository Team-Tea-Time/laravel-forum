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

        $presetRoot = __DIR__."/../../../ui-presets/{$preset}";

        if (!$filesystem->exists($presetRoot))
        {
            $this->error("The preset '{$preset}' does not exist.");
            return;
        }

        $filesystem->ensureDirectoryExists(resource_path("forum/views/{$preset}"));
        $filesystem->copyDirectory($presetRoot, resource_path("forum/views/{$preset}"));

        // $filesystem->ensureDirectoryExists(resource_path('views/forum'));
        // $filesystem->copyDirectory(__DIR__."/../../../ui-presets/{$preset}/resources/views", resource_path('views'));

        // $filesystem->ensureDirectoryExists(resource_path('views/components/forum'));
        // $filesystem->copyDirectory(__DIR__."/../../../ui-presets/{$preset}/resources/views/components/forum", resource_path('views/components'));

        $this->info("Preset '{$preset}' has been copied to your application's resource directory.");
    }
}
