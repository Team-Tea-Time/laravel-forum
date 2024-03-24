<?php

namespace TeamTeaTime\Forum\Presets;

class PresetRegistry
{
    private array $presets = [];

    public function register(AbstractPreset $preset)
    {
        $this->presets[$preset->name()] = $preset;
    }

    public function get(string $name): AbstractPreset
    {
        if (! isset($this->presets[$name])) {
            throw new \Exception("Preset '{$name}' not found. Check the name and ensure the preset is registered.");
        }

        return $this->presets[$name];
    }

    public function getAll(): array
    {
        return $this->presets;
    }
}
