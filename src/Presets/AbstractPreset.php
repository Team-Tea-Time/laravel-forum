<?php

namespace TeamTeaTime\Forum\Presets;

use Illuminate\Filesystem\Filesystem;
use TeamTeaTime\Forum\Config\FrontendStack;

abstract class AbstractPreset
{
    abstract public function name(): string;

    abstract public function description(): string;

    abstract public function requiredStack(): FrontendStack;

    public function isValid(): bool
    {
        return file_exists($this->sourcePath());
    }

    protected function sourcePath(): string
    {
        return __DIR__."/../../ui-presets/{$this->name()}";
    }

    protected function destinationPath(): string
    {
        return resource_path("forum/{$this->name()}");
    }

    public function publish(FileSystem $filesystem): void
    {
        $destinationPath = $this->destinationPath();
        $filesystem->ensureDirectoryExists($destinationPath);
        $filesystem->copyDirectory($this->sourcePath(), $destinationPath);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'description' => $this->description(),
            'required_stack' => $this->requiredStack()->value,
        ];
    }
}
