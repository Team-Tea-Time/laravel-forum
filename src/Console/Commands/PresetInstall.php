<?php

namespace TeamTeaTime\Forum\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use TeamTeaTime\Forum\Frontend\Presets\AbstractPreset;
use TeamTeaTime\Forum\Frontend\Presets\PresetRegistry;

class PresetInstall extends Command
{
    protected $signature = 'forum:preset-install {name}';

    protected $description = 'Install a frontend preset for the forum.';

    public function handle(Filesystem $filesystem)
    {
        $name = $this->argument('name');

        /**
         * @var PresetRegistry $presets
         */
        $presets = $this->laravel->make(PresetRegistry::class);

        /**
         * @var AbstractPreset $preset
         */
        $preset;
        try {
            $preset = $presets->get($name);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        if (!$preset->isValid()) {
            $this->error("This preset is not valid. It may have incorrect or missing paths.");
            return;
        }

        $preset->publish($filesystem);

        $this->info("Preset '{$name}' has been copied to your application's resource directory.");
    }
}
