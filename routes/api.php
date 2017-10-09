<?php

Route::group([ 'middleware' => 'guest' ], function () {
	Route::post('registration', [
		'as'   => 'post.registration',
		'uses' => 'AuthController@postRegistration',
	]);

	Route::post('login', [
		'as'   => 'post.login',
		'uses' => 'AuthController@postLogin',
	]);
});

Route::group([ 'middleware' => 'auth:token' ], function () {
	Route::post('refresh', [
		'as'   => 'post.refreshToken',
		'uses' => 'AuthController@postRefreshToken',
	]);
});