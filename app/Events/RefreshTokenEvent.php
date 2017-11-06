<?php namespace App\Events;

use App\Models\Token;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshTokenEvent
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $previousAccessToken, $newAccessToken;

	public function __construct (Token $previousAccessToken, Token $newAccessToken) {
		$this->previousAccessToken = $previousAccessToken;
		$this->newAccessToken = $newAccessToken;
	}

	public function broadcastOn () {
		return new PrivateChannel('channel-name');
	}
}
