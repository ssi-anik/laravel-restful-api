<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleTagTable extends Migration
{
	public function up () {
		Schema::create('article_tag', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('tag_id');
			$table->unsignedInteger('article_id');
		});
	}

	public function down () {
		Schema::dropIfExists('article_tag');
	}
}
