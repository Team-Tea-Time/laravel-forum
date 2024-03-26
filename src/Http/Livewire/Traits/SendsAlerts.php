<?php

namespace TeamTeaTime\Forum\Http\Livewire\Traits;

use TeamTeaTime\Forum\Http\Livewire\Types\Alert;
use TeamTeaTime\Forum\Http\Livewire\Types\AlertType;

trait SendsAlerts
{
    public function invalidSelectionAlert(): Alert
    {
        return new Alert(AlertType::Warning, trans('forum::general.invalid_selection'));
    }

    public function transChoiceAlert(string $key, int $count): Alert
    {
        return new Alert(AlertType::Success, trans_choice("forum::{$key}", $count));
    }
}
