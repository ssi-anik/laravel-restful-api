<?php namespace App\Providers;

use App\Events\RefreshTokenEvent;
use App\Listeners\RefreshTokenEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	protected $listen = [
		RefreshTokenEvent::class => [
			RefreshTokenEventListener::class,
		],
	];

	public function boot () {
		parent::boot();

		//
	}
}
