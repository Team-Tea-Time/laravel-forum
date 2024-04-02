<?php

namespace TeamTeaTime\Forum\Console\Commands;

use Illuminate\Console\Command;
use TeamTeaTime\Forum\Frontend\Presets\AbstractPreset;
use TeamTeaTime\Forum\Frontend\Presets\PresetRegistry;

use function Laravel\Prompts\{
    info,
    table,
};

class PresetList extends Command
{
    protected $signature = 'forum:preset-list';

    protected $description = 'List available frontend presets';

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

        table(['Name', 'Summary', 'Required Stack'], $table);

        info("Install a preset with: php artisan forum:preset-install {preset}.");
    }
}
