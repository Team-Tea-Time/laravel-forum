<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTableMessages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_messages', function(Blueprint $table) 
		{
			$table->increments('id');
			
			/* Attributes */
			$table->integer('parent_topic')->unsigned();
			$table->integer('author_id')->unsigned();
			$table->text('data');

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
		Schema::drop('forum_messages');
	}

}
