<?php

namespace TeamTeaTime\Forum\Http\Livewire\Traits;

trait UpdatesContent
{
    /**
     * A unique string used to trigger client-side DOM updates when content is changed.
     */
    public string $updateKey;

    private function touchUpdateKey()
    {
        $this->updateKey = uniqid();
    }
}
