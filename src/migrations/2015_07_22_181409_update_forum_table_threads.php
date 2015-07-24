<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForumTableThreads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forum_threads', function (Blueprint $table)
        {
            $table->renameColumn('parent_category', 'category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forum_threads', function (Blueprint $table)
        {
            $table->renameColumn('category_id', 'parent_category');
        });
    }
}
