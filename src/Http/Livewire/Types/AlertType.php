<?php

namespace TeamTeaTime\Forum\Http\Livewire\Types;

enum AlertType: string
{
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Error = 'error';
}
