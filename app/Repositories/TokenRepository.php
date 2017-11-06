<?php namespace App\Repositories;

use App\Extensions\Helper;
use App\Models\Token;
use Carbon\Carbon;

class TokenRepository
{
	private $model = null;

	public function __construct (Token $token) {
		$this->model = $token;
	}

	public function saveNewToken ($userId) {
		return Token::create([
			'user_id'       => $userId,
			'access_token'  => Helper::generateUniqueString(),
			'refresh_token' => Helper::generateUniqueString(),
			'expires_in'    => Carbon::now()->addDays(env('TOKEN_VALIDATION_IN_DAYS', 3)),
		]);
	}

	public function matchAccessTokenWithRefreshToken ($accessToken, $refreshToken, $userId) {
		return $this->model->where('access_token', $accessToken)
						   ->where('refresh_token', $refreshToken)
						   ->where('user_id', $userId)
						   ->first();
	}

	public function invalidateAccessToken (Token $token) {
		$token->update([ 'expires_in' => Carbon::now()->subDays(1) ]);
		return true;
	}
}