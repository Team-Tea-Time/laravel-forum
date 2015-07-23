<?php namespace Riari\Forum\Models\Traits;

trait HasAuthor
{
    /**
     * @var object
     */
    protected $author;

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

        if (!is_null($this->author) && isset($this->author->$attribute)) {
            return $this->author->$attribute;
        }

        return null;
    }
}
