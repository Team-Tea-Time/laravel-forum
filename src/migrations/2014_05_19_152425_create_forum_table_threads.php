<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTableThreads extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_threads', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('category_id')->unsigned();
			$table->integer('author_id')->unsigned();
			$table->string('title');
			$table->integer('pinned')->unsigned();
			$table->integer('locked')->unsigned();

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
		Schema::drop('forum_threads');
	}
}
