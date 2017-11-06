<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
	public function up () {
		Schema::create('articles', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->string('slug',50)->unique();
			$table->string('title', 100);
			$table->text('content');
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down () {
		Schema::dropIfExists('articles');
	}
}
