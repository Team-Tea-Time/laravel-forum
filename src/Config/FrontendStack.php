<?php

namespace TeamTeaTime\Forum\Config;

enum FrontendStack: string
{
    case NONE = 'none';
    case BLADE = 'blade';
    case LIVEWIRE = 'livewire';
}
