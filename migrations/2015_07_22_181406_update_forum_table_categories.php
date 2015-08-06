<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForumTableCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forum_categories', function (Blueprint $table)
        {
            $table->renameColumn('parent_category', 'category_id');
			$table->string('subtitle')->nullable()->change();
            $table->integer('weight')->nullable()->change();
            $table->boolean('allows_threads');

            $table->timestamps();
            $table->softDeletes();
        });

		DB::table('forum_categories')->insert(
			[
				['parent_category' => null, 'title' => 'Top level category', 'subtitle' => 'Contains categories and threads', 'weight' => 0, 'allows_threads' => 1],
				['parent_category' => 1, 'title' => 'Level 1 child category', 'subtitle' => 'Contains threads', 'weight' => 0, 'allows_threads' => 1],
				['parent_category' => 2, 'title' => 'Level 2 child category', 'subtitle' => 'Contains more threads', 'weight' => 1, 'allows_threads' => 1]
			]
		);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forum_categories', function (Blueprint $table)
        {
            $table->renameColumn('category_id', 'parent_category');
            $table->dropColumn(['created_at', 'updated_at', 'deleted_at']);
        });
    }
}
