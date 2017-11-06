<?php namespace App\Events;

use App\Models\Token;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshTokenEvent
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public function __construct (Token $token) {
		$this->token = $token;
	}

	public function broadcastOn () {
		return new PrivateChannel('channel-name');
	}
}
