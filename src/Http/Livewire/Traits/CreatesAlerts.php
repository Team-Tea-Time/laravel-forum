<?php

namespace TeamTeaTime\Forum\Http\Livewire\Traits;

use TeamTeaTime\Forum\Http\Livewire\Types\Alert;
use TeamTeaTime\Forum\Http\Livewire\Types\AlertType;

trait CreatesAlerts
{
    public function invalidSelectionAlert(): Alert
    {
        return new Alert(AlertType::Warning, trans('forum::general.invalid_selection'));
    }

    public function alert(string $key, AlertType $type = AlertType::Success): Alert
    {
        return new Alert($type, trans("forum::{$key}"));
    }

    public function pluralAlert(string $key, int $count, AlertType $type = AlertType::Success): Alert
    {
        return new Alert($type, trans_choice("forum::{$key}", $count));
    }
}
