<?php namespace Riari\Forum\Models\Traits;

trait HasAuthor
{
    public function author()
    {
        return $this->belongsTo(config('forum.integration.user_model'), 'author_id');
    }

    public function getAuthorNameAttribute()
    {
        $attribute = config('forum.integration.user_name_attribute');

        if (!is_null($this->author) && isset($this->author->$attribute)) {
            return $this->author->$attribute;
        }

        return null;
    }
}
