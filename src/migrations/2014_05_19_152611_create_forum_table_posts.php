<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTablePosts extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_posts', function (Blueprint $table)
		{
			$table->increments('id');
			$table->integer('thread_id')->unsigned();
			$table->integer('author_id')->unsigned();
			$table->text('content');

			$table->foreign('thread_id')
				->references('id')->on('forum_threads')
				->onDelete('cascade');

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
		Schema::drop('forum_posts');
	}
}
