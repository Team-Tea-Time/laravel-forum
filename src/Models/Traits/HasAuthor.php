<?php namespace TeamTeaTime\Forum\Models\Traits;

trait HasAuthor
{
    /**
     * Relationship: Author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        $model = config('forum.integration.user_model');
        if (method_exists($model, 'withTrashed')) {
            return $this->belongsTo($model, 'author_id')->withTrashed();
        }

        return $this->belongsTo($model, 'author_id');
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
