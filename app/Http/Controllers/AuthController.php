<?php namespace App\Http\Controllers;

use App\Events\RefreshTokenEvent;
use App\Extensions\Helper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Requests\RegistrationRequest;
use App\Repositories\TokenRepository;
use App\Repositories\UserRepository;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
	public function postRegistration (RegistrationRequest $request, UserRepository $userRepository, TokenRepository $tokenRepository, CacheService $cacheService) {
		$form = [
			'name'     => trim($request->get('name')),
			'email'    => trim($request->get('email')),
			'password' => $request->get('password'),
		];

		// check if profile picture exits
		if ($request->hasFile('profile_picture')) {
			$imageName = sprintf('%s.png', str_random(30));
			$imagePath = "images/{$imageName}";
			$image = Image::make($request->file('profile_picture'));
			$image->resize(30, 30);
			$image->save(storage_path($imagePath));
			$form['profile_picture'] = $imagePath;
		}

		$user = $userRepository->saveNewUser($form);
		// write request to log file
		Helper::logRequest($form, $request->route()->getName());
		// generate access token
		$token = $tokenRepository->saveNewToken($user->id);
		// cache access token for 5 min with user id as value
		$cacheService->insertAccessTokenToCache($token->access_token, $token->user_id, 5);

		return $this->respondSuccess([
			'user_id'       => $user->id,
			'name'          => $user->name,
			'email'         => $user->email,
			'access_token'  => $token->access_token,
			'refresh_token' => $token->refresh_token,
			'expires_in'    => $token->expires_in->toDateTimeString(),
		], 201);
	}

	public function postLogin (LoginRequest $request, TokenRepository $tokenRepository, CacheService $cacheService) {
		$credentials = [
			'email'      => $request->get('email'),
			'password'   => $request->get('password'),
			'deleted_at' => null,
		];

		$isAccepted = Auth::attempt($credentials);
		if (false === $isAccepted) {
			return $this->respondError([ 'message' => 'Email or Password mismatch', ], 400);
		}

		$user = Auth::user();
		// save access token
		$token = $tokenRepository->saveNewToken($user->id);
		// cache access token for 5 min with user id as value
		$cacheService->insertAccessTokenToCache($token->access_token, $token->user_id, 5);

		return $this->respondSuccess([
			'user_id'       => $user->id,
			'name'          => $user->name,
			'email'         => $user->email,
			'access_token'  => $token->access_token,
			'refresh_token' => $token->refresh_token,
			'expires_in'    => $token->expires_in->toDateTimeString(),
		], 200);
	}

	public function postRefreshToken (RefreshTokenRequest $request, TokenRepository $tokenRepository, CacheService $cacheService) {
		$accessToken = $request->bearerToken() ?: $request->get('access_token');
		$refreshToken = $request->get('refresh_token');

		$user = Auth::user();
		$previousAccessToken = $tokenRepository->matchAccessTokenWithRefreshToken($accessToken, $refreshToken, $user->id);

		if (!$previousAccessToken || Carbon::now()->gt(new Carbon($previousAccessToken->expires_in))) {
			return $this->respondError([ 'token' => 'Invalid Token' ]);
		}

		$newToken = $tokenRepository->saveNewToken($user->id);
		event(new RefreshTokenEvent($previousAccessToken, $newToken));
		$cacheService->insertAccessTokenToCache($newToken->access_token, $newToken->user_id, 5);

		return $this->respondSuccess([
			'user_id'       => $user->id,
			'name'          => $user->name,
			'email'         => $user->email,
			'access_token'  => $newToken->access_token,
			'refresh_token' => $newToken->refresh_token,
			'expires_in'    => $newToken->expires_in->toDateTimeString(),
		], 200);
	}

    public function validateToken (Request $request, TokenRepository $tokenRepository) {
        $userAccessToken = $request->bearerToken() ?: $request->get('access_token');

        $user = Auth::user();
        $token = $tokenRepository->validateAccessToken($userAccessToken);

        if (!$token) {
            return $this->respondError([ 'token' => 'Invalid Token' ], 400);
        }

        return $this->respondSuccess([
            'user_id'      => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'access_token' => $token->access_token,
            'expires_in'   => $token->expires_in,
        ], 200);
    }
}
