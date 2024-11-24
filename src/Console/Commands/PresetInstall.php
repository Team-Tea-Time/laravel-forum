<?php

namespace TeamTeaTime\Forum\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use TeamTeaTime\Forum\Frontend\Presets\AbstractPreset;
use TeamTeaTime\Forum\Frontend\Presets\PresetRegistry;

use function Laravel\Prompts\{
    alert,
    confirm,
    error,
    info,
    note,
    select,
    warning,
};

class PresetInstall extends Command implements PromptsForMissingInput
{
    protected $signature = 'forum:preset-install {name}';

    protected $description = 'Install a frontend preset';

    private PresetRegistry $presets;

    public function __construct()
    {
        parent::__construct();

        $this->presets = resolve(PresetRegistry::class);
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        $options = [];
        foreach ($this->presets->getAll() as $preset) {
            $options[$preset->getName()] = $preset->getSummary();
        }

        return [
            'name' => fn () => select(
                label: 'Which preset would you like to install?',
                options: $options,
            ),
        ];
    }

    public function handle(Filesystem $filesystem)
    {
        $name = $this->argument('name');

        /**
         * @var AbstractPreset $preset
         */
        $preset;
        try {
            $preset = $this->presets->get($name);
        } catch (\Exception $e) {
            error($e->getMessage());
            return;
        }

        if (!$preset->isValid()) {
            error("This preset is not valid. It may have incorrect or missing paths.");
            return;
        }

        $confirmMessage = file_exists($preset->getDestinationPath())
            ? "It looks like this preset's destination directory already exists. Proceed?"
            : "Install this preset?";

        if (!confirm(label: $confirmMessage)) {
            info("Cancelled.");
            return;
        }

        $preset->publish($filesystem);

        info("Done! Preset '{$name}' has been copied to your application's resource directory.");

        alert("IMPORTANT: Please read the info below as it details manual steps required for the preset to work!");

        $requiredPackages = $preset->getRequiredPackages();

        if (count($requiredPackages) > 0) {
            info("This preset requires the following NPM packages:");
            foreach ($requiredPackages as $package) {
                $this->line("    $package");
            }

            info("You can install them with:");
            note("npm i " . implode(' ', $requiredPackages));

            if (in_array("tailwindcss", $requiredPackages) && !file_exists(base_path("tailwind.config.js"))) {
                warning("This preset requires Tailwind, but it looks like your app doesn't have a Tailwind configuration. You should set it up either by following the guide at https://tailwindcss.com/docs/guides/laravel or by installing a Laravel starter kit that includes it, such as Laravel Breeze: https://laravel.com/docs/11.x/starter-kits#breeze-and-blade");
            }
        }

        $viteInput = $preset->getViteInput();

        if (count($viteInput) > 0) {
            info("This preset requires the following lines need to be added to the Laravel input array in your vite.config.js file:");
            foreach ($viteInput as $input) {
                $this->line("    '$input',");
            }

            info("After adding these lines, don't forget to build for production:");
            note("npm run build");
        }

        info("To activate this preset, make sure the package config is published to your app and set the forum.frontend.preset value to '{$preset->getName()}'.");
    }
}
