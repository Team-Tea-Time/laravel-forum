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
            $table->integer('category_id')->default(0)->change();
            $table->string('subtitle')->nullable()->change();
            $table->integer('weight')->default(0)->change();

            $table->boolean('enable_threads')->default(0);
            $table->boolean('private')->default(0);

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
