<?php namespace App\Listeners;

use Carbon\Carbon;

class RefreshTokenEventListener
{
	public function __construct () {
	}

	public function handle ($event) {
		// invalidate the given access token
		$event->token->update([ 'expires_in' => Carbon::now()->subDays(1) ]);
	}
}
