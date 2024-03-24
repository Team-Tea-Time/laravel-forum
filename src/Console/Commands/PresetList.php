<?php

namespace TeamTeaTime\Forum\Console\Commands;

use Illuminate\Console\Command;
use TeamTeaTime\Forum\Frontend\Presets\AbstractPreset;
use TeamTeaTime\Forum\Frontend\Presets\PresetRegistry;

class PresetList extends Command
{
    protected $signature = 'forum:preset-list';

    protected $description = 'List available forum frontend presets.';

    public function handle()
    {
        /**
         * @var PresetRegistry $registry
         */
        $registry = $this->laravel->make(PresetRegistry::class);

        /**
         * @var AbstractPreset[] $presets
         */
        $presets = $registry->getAll();

        $table = [];
        foreach ($presets as $preset) {
            $table[] = $preset->toArray();
        }

        $this->table(['Name', 'Description', 'Required Stack'], $table);

        $this->info("Install a preset with: php artisan forum:preset:install {preset}.");
    }
}
