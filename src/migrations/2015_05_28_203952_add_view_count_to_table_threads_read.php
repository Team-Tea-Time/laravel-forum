<?php

use Illuminate\Database\Migrations\Migration;

class AddViewCountToTableThreadsRead extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forum_threads_read', function($table)
        {
            $table->integer('view_count')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forum_threads_read', function($table)
        {
            $table->dropColumn('view_count');
        });
    }

}
