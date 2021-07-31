<?php

namespace TeamTeaTime\Forum\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'accepts_threads' => $this->accepts_threads == 1,
            'newest_thread_id' => $this->newest_thread_id,
            'latest_active_thread_id' => $this->latest_active_thread_id,
            'thread_count' => $this->thread_count,
            'post_count' => $this->post_count,
            'is_private' => $this->is_private == 1,
            'left' => $this->_lft,
            'right' => $this->_rgt,
            'parent_id' => $this->parent_id,
            'color' => $this->color,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
