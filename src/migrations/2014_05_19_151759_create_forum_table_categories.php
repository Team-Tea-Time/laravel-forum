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
			$table->integer('weight');
		});

		DB::table('forum_categories')->insert(
			array(
				[1, NULL, 'Category', 'Contains categories and threads', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'],
				[2, 1, 'Sub-category', 'Contains threads', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'],
				[3, 1, 'Second subcategory', 'Contains more threads', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00']
			)
		);
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
