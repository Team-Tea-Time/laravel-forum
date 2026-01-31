<?php

namespace TeamTeaTime\Forum\Tests\Feature\Web;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Orchestra\Testbench\Factories\UserFactory;
use PHPUnit\Framework\Attributes\Test;
use TeamTeaTime\Forum\Database\Factories\CategoryFactory;
use TeamTeaTime\Forum\Database\Factories\PostFactory;
use TeamTeaTime\Forum\Database\Factories\ThreadFactory;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Frontend\Forum;
use TeamTeaTime\Forum\Tests\FeatureTestCase;

class BulkActionsTest extends FeatureTestCase
{
    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->createOne();
        $this->category = CategoryFactory::new()->createOne();

        // Grant administrative permissions
        Gate::define('approveThreads', fn () => true);
        Gate::define('deleteThreads', fn () => true);
        Gate::define('approvePosts', fn () => true);
        Gate::define('deletePosts', fn () => true);
        Gate::define('viewTrashedPosts', fn () => true);
        
        // Fix for PostAuthorization checks on individual items
        Gate::define('view', fn () => true);
        Gate::define('delete', fn () => true);
    }

    #[Test]
    public function should_bulk_approve_threads()
    {
        $threads = ThreadFactory::new()->count(3)->create([
            'category_id' => $this->category->id,
            'author_id' => $this->user->id,
            'approved_at' => null // Pending approval
        ]);

        $response = $this->actingAs($this->user)
            ->post(Forum::route('forum.bulk.thread.approve'), [
                'threads' => $threads->pluck('id')->toArray()
            ]);

        $response->assertRedirect();
        
        foreach ($threads as $thread) {
            $this->assertNotNull($thread->fresh()->approved_at);
        }
    }

    #[Test]
    public function should_bulk_delete_threads()
    {
        $threads = ThreadFactory::new()->count(3)->create([
            'category_id' => $this->category->id,
            'author_id' => $this->user->id,
            'approved_at' => null // Pending approval
        ]);

        $response = $this->actingAs($this->user)
            ->delete(Forum::route('forum.bulk.thread.delete'), [
                'threads' => $threads->pluck('id')->toArray()
            ]);

        $response->assertRedirect();

        foreach ($threads as $thread) {
            $this->assertNotNull($thread->fresh()->deleted_at);
        }
    }

    #[Test]
    public function should_bulk_approve_posts()
    {
        $thread = ThreadFactory::new()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->user->id,
        ]);

        $posts = PostFactory::new()->count(3)->create([
            'thread_id' => $thread->id,
            'author_id' => $this->user->id,
            'approved_at' => null // Pending approval
        ]);

        $response = $this->actingAs($this->user)
            ->post(Forum::route('forum.bulk.post.approve'), [
                'posts' => $posts->pluck('id')->toArray()
            ]);

        $response->assertRedirect();

        foreach ($posts as $post) {
            $this->assertNotNull($post->fresh()->approved_at);
        }
    }

    #[Test]
    public function should_bulk_delete_posts()
    {
        $thread = ThreadFactory::new()->create([
            'category_id' => $this->category->id,
            'author_id' => $this->user->id,
        ]);

        // Sequence must be > 1 for generic posts as first post is the thread
        $posts = PostFactory::new()->count(3)->sequence(
            ['sequence' => 2],
            ['sequence' => 3],
            ['sequence' => 4]
        )->create([
            'thread_id' => $thread->id,
            'author_id' => $this->user->id,
            'approved_at' => null // Pending approval
        ]);

        $response = $this->actingAs($this->user)
            ->delete(Forum::route('forum.bulk.post.delete'), [
                'posts' => $posts->pluck('id')->toArray()
            ]);

        $response->assertRedirect();

        foreach ($posts as $post) {
            $this->assertNotNull($post->fresh()->deleted_at);
        }
    }
}
