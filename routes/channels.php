<?php

use Illuminate\Support\Facades\Broadcast;
use TeamTeaTime\Forum\Broadcasting\CategoryChannel;

Broadcast::channel('Forum.Category.{id}', CategoryChannel::class);
