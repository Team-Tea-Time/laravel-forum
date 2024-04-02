<?php

namespace TeamTeaTime\Forum\Frontend\Presets;

use Illuminate\Filesystem\Filesystem;
use TeamTeaTime\Forum\Config\FrontendStack;

abstract class AbstractPreset
{
    abstract public static function getName(): string;

    /**
     * Returns a short summary of the preset.
     * Shown in the forum:preset-list table.
     */
    abstract public static function getSummary(): string;

    abstract public static function getRequiredStack(): FrontendStack;

    /**
     * Returns any NPM packages required by the preset.
     */
    public static function getRequiredPackages(): array
    {
        return [];
    }

    /**
     * Registers any components required by the preset.
     */
    public function register(): void
    {
    }

    public function isValid(): bool
    {
        return file_exists($this->getSourcePath());
    }

    public function getSourcePath(): string
    {
        return __DIR__ . "/../../../ui-presets/{$this->getName()}";
    }

    public function getDestinationPath(): string
    {
        return resource_path("forum/{$this->getName()}");
    }

    public function getViewsPath(): string
    {
        return $this->getDestinationPath() . '/views';
    }

    public function publish(FileSystem $filesystem): void
    {
        $destinationPath = $this->getDestinationPath();
        $filesystem->ensureDirectoryExists($destinationPath);
        $filesystem->copyDirectory($this->getSourcePath(), $destinationPath);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getSummary(),
            'required_stack' => $this->getRequiredStack()->value,
        ];
    }
}
