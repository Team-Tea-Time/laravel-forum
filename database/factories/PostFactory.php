<?php

namespace TeamTeaTime\Forum\Database\Factories;

use TeamTeaTime\Forum\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'thread_id' => ThreadFactory::new(),
            'author_id' => 0,
            'post_id' => null,
            'content' => $this->faker->text
        ];
    }
}
