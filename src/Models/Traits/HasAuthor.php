<?php namespace Riari\Forum\Models\Traits;

trait HasAuthor
{
    /**
     * Relationship: Author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(config('forum.integration.user_model'));
    }

    /**
     * Attribute: Author name.
     *
     * @return mixed
     */
    public function getAuthorNameAttribute()
    {
        $attribute = config('forum.integration.user_name');

        if (!is_null($this->author)) {
            return $this->author->$attribute;
        }

        return null;
    }
}
