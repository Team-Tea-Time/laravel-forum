<?php

namespace TeamTeaTime\Forum\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use TeamTeaTime\Forum\Events\Types\ThreadEvent;

class UserCreatedThread extends ThreadEvent implements ShouldBroadcast
{
    public function broadcastAs(): string
    {
        return 'user-created-thread';
    }

    public function broadcastOn(): Channel
    {
        $channel = "Forum.Category.{$this->thread->category_id}";
        return new PrivateChannel($channel);
    }
}
