<?php namespace App\Providers;

use App\Extensions\AccessTokenGuard;
use App\Extensions\TokenToUserProvider;
use App\Models\Article;
use App\Policies\ArticlePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
	protected $policies = [
		Article::class => ArticlePolicy::class,
	];


	public function boot () {
		$this->registerPolicies();

		Auth::extend('access_token', function ($app, $name, array $config) {
			// automatically build the DI, put it as reference
			$userProvider = app(TokenToUserProvider::class);
			$request = app('request');

			return new AccessTokenGuard($userProvider, $request, $config);
		});
	}
}
