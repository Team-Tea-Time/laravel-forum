<?php

namespace TeamTeaTime\Forum\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'category_id' => $this->category_id,
                'author_id' => $this->author_id,
                'title' => $this->title,
                'pinned' => $this->pinned == 1,
                'locked' => $this->locked == 1,
                'first_post_id' => $this->first_post_id,
                'last_post_id' => $this->last_post_id,
                'reply_count' => $this->reply_count,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'deleted_at' => $this->deleted_at
            ],
            'links' => [
                'posts' => route(config('forum.api.router.as') . 'thread.posts', ['thread' => $this->id])
            ]
        ];
    }
}