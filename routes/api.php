<?php

Route::group([ 'middleware' => 'guest' ], function () {
	Route::post('registration', [
		'as'   => 'registration.post',
		'uses' => 'AuthController@postRegistration',
	]);
});
