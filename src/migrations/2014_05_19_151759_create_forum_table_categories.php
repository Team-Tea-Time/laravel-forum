<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTableCategories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_categories', function(Blueprint $table) 
		{
			$table->increments('id');
			$table->integer('parent_category')->unsigned()->nullable();
			$table->string('title');
			$table->string('subtitle');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('forum_categories');
	}

}
