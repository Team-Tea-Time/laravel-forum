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
