<?php

namespace Riari\Forum\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Attribute: Slug
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return Str::slug($this->title);
    }
}
