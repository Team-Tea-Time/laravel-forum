<?php

namespace TeamTeaTime\Forum\Http\Livewire\Traits;

trait HandlesBulkActions
{
    private function handleActionResult($result, string $key = 'threads.updated'): array
    {
        if ($result == null) {
            return $this->invalidSelectionAlert()->toLivewire();
        }

        $this->touchUpdateKey();

        return $this->pluralAlert($key, $result->count())->toLivewire();
    }
}
