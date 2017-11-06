<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
	public function up () {
		Schema::create('tokens', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->string('access_token', 64);
			$table->string('refresh_token', 64);
			$table->dateTime('expires_in');
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down () {
		Schema::dropIfExists('tokens');
	}
}
