<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTableCategories extends Migration
{
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
			$table->integer('parent_id')->unsigned()->nullable();
			$table->string('title');
			$table->string('subtitle');
			$table->integer('weight');
		});

		DB::table('forum_categories')->insert(
			[
				['parent_id' => null, 'title' => 'Category', 'subtitle' => 'Contains categories and threads', 'weight' => 0],
				['parent_id' => 1, 'title' => 'Sub-category', 'subtitle' => 'Contains threads', 'weight' => 0],
				['parent_id' => 1, 'title' => 'Second subcategory', 'subtitle' => 'Contains more threads', 'weight' => 1]
			]
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
