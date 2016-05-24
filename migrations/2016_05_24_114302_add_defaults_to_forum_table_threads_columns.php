<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultsToForumTableThreadsColumns extends Migration
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
            $table->column('pinned')->nullable()->default(0)->change();
            $table->column('locked')->nullable()->default(0)->change();
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
            $table->column('pinned')->nullable(false)->default(null)->change();
            $table->column('locked')->nullable(false)->default(null)->change();
        });
    }
}
