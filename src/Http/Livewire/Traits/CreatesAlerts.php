<?php

namespace TeamTeaTime\Forum\Http\Livewire\Traits;

use TeamTeaTime\Forum\Http\Livewire\Types\Alert;
use TeamTeaTime\Forum\Http\Livewire\Types\AlertType;

trait CreatesAlerts
{
    protected function invalidSelectionAlert(): Alert
    {
        return new Alert(AlertType::Warning, trans('forum::general.invalid_selection'));
    }

    protected function alert(string $key, AlertType $type = AlertType::Success): Alert
    {
        return new Alert($type, trans("forum::{$key}"));
    }

    protected function pluralAlert(string $key, int $count = 1, AlertType $type = AlertType::Success): Alert
    {
        return new Alert($type, trans_choice("forum::{$key}", $count));
    }
}
