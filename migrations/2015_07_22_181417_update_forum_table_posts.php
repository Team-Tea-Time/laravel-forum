<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForumTablePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forum_posts', function (Blueprint $table)
        {
            $table->renameColumn('parent_thread', 'thread_id');
            $table->integer('post_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forum_posts', function (Blueprint $table)
        {
            $table->renameColumn('thread_id', 'parent_thread');
        });
    }
}
