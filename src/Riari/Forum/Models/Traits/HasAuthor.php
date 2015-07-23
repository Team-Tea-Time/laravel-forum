<?php namespace Riari\Forum\Models\Traits;

trait HasAuthor
{
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function author()
    {
        return $this->belongsTo(config('forum.integration.models.user'));
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    public function getAuthorNameAttribute()
    {
        $attribute = config('forum.integration.user.attributes.name');

        if (!is_null($this->author)) {
            return $this->author->$attribute;
        }

        return null;
    }
}
