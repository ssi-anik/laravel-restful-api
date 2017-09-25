<?php namespace App\Repositories;

use App\Helpers\Logger;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;

class TokenRepository
{
	private $model = null;

	public function __construct (Token $token) {
		$this->model = $token;
	}

	public function saveNewToken (User $user) {
		return Token::create([
			'user_id'       => $user->id,
			'access_token'  => Logger::generateUniqueString(),
			'refresh_token' => Logger::generateUniqueString(),
			'expires_in'    => Carbon::now()->addDays(env('TOKEN_VALIDATION_IN_DAYS')),
		]);
	}
}