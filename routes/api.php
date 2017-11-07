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

	Route::resource('article', 'ArticleController', [ 'only' => [ 'store', 'update', 'destroy' ] ]);
});

// routes those don't require any guest or auth middleware
Route::group([ 'middleware' => [] ], function () {
	Route::resource('article', 'ArticleController', [ 'only' => [ 'index', 'show', ] ]);
	Route::resource('user.article', 'UserArticleController', ['only' => ['index']]);
});