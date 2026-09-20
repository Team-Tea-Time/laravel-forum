<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Aimeos\Nestedset\NestedSet;

class UpdateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->nestedSet();
            $table->dropColumn(['category_id', 'weight']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            // Note: drop only the default nested set columns here. The 'depth'
            // column is owned by the dedicated migrations that add it, and is
            // dropped there to keep rollbacks idempotent.
            NestedSet::dropColumns($table);
            $table->integer('category_id')->unsigned();
            $table->integer('weight');
        });
    }
}
