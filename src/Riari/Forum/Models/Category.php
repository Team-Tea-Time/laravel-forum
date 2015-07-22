<?php namespace Riari\Forum\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Riari\Forum\Models\Thread;

class Category extends BaseModel
{
    use SoftDeletes;

    // Eloquent properties
    protected $table      = 'forum_categories';
    public    $timestamps = false;
    protected $appends    = ['threadCount', 'replyCount', 'route', 'newThreadRoute'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function parent()
    {
        return $this->belongsTo('\Riari\Forum\Models\Category', 'category_id')->orderBy('weight');
    }

    public function children()
    {
        return $this->hasMany('\Riari\Forum\Models\Category', 'category_id')->with('threads')->orderBy('weight');
    }

    public function threads()
    {
        return $this->hasMany('\Riari\Forum\Models\Thread')->with('category', 'posts');
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    // Route attributes

    public function getRouteAttribute()
    {
        return $this->getRoute('forum.category.index');
    }

    public function getNewThreadRouteAttribute()
    {
        return $this->getRoute('forum.thread.create');
    }

    // General attributes

    public function getThreadsPaginatedAttribute()
    {
        return $this->threads()
            ->orderBy('pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(config('forum.preferences.pagination.threads'));
    }

    public function getPageLinksAttribute()
    {
        return $this->threadsPaginated->render();
    }

    public function getNewestThreadAttribute()
    {
        return $this->threads()->orderBy('created_at', 'desc')->first();
    }

    public function getLatestActiveThreadAttribute()
    {
        return $this->threads()->orderBy('updated_at', 'desc')->first();
    }

    public function getThreadCountAttribute()
    {
        return $this->rememberAttribute('threadCount', function(){
            return $this->threads->count();
        });
    }

    public function getPostCountAttribute()
    {
        return $this->rememberAttribute('postCount', function(){
            $replyCount = 0;

            $threads = $this->threads()->get(['id']);

            foreach ($threads as $thread) {
                $replyCount += $thread->posts->count() - 1;
            }

            return $replyCount;
        });
    }

    // Current user: permission attributes

    public function getUserCanViewAttribute()
    {
        return $this->userCan('forum.category.index');
    }

    public function getUserCanCreateThreadsAttribute()
    {
        return $this->userCan('forum.thread.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Return an array of components used to construct this model's route.
     *
     * @return array
     */
    protected function getRouteComponents()
    {
        $components = [
            'category'  	=> $this->id,
            'categoryAlias' => Str::slug($this->title, '-')
        ];

        return $components;
    }

    /**
     * Return an array of parameters used by the userCan() method to check
     * permissions.
     *
     * @return array
     */
    protected function getAccessParams()
    {
        $parameters = ['category' => $this];

        return $parameters;
    }
}
