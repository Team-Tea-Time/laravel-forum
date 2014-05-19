<?php namespace Atrakeur\Forum\Models;

class ForumCategory extends \Eloquent
{
    protected $table = 'forum_categories';
    public $timestamps = false;
    protected $softDelete = false;

    public function parentCategory()
    {
        return $this->hasOne('\Atrakeur\Forum\Models\ForumCategory', 'parent_category');
    }

    public function subcategories()
    {
        return $this->hasMany('\Atrakeur\Forum\Models\ForumCategory', 'parent_category');
    }

    public function forumtopic()
    {
        return $this->hasMany('\Atrakeur\Forum\Models\ForumTopic');
    }

    public function scopeWhereTopLevel($query) {
        return $query->where('parent_category', '=', NULL);
    }

}