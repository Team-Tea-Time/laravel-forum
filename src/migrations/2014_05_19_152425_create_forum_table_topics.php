<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTableTopics extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_topics', function(Blueprint $table) 
		{
			$table->increments('id');

			/* Attributes */
			$table->integer('parent_category')->unsigned();
			$table->integer('author')->unsigned();
			$table->string('title');

			/* Cached attributes */
			$table->integer('cache_reply_count')->unsigned();

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
		Schema::drop('forum_topics');
	}

}
