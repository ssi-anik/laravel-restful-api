<?php namespace App\Http\Controllers;

use App\Extensions\Helper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Repositories\TokenRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\CacheService;
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
		$token = $tokenRepository->saveNewToken($user);
		// cache access token for 5 min with user id as value
		$cacheService->insertAccessTokenToCache($token->access_token, $token->user_id, 5);

		return response()->json([
			'user_id'       => $user->id,
			'name'          => $user->name,
			'email'         => $user->email,
			'access_token'  => $token->access_token,
			'refresh_token' => $token->refresh_token,
			'expires_in'    => $token->expires_in->toDateTimeString(),
		], 201);
	}

	public function postLogin (LoginRequest $request, TokenRepository $tokenRepository, AuthService $authService, CacheService $cacheService) {
		$credentials = [
			'email'      => $request->get('email'),
			'password'   => $request->get('password'),
			'deleted_at' => null,
		];

		$user = $authService->check($credentials);

		if (false === $user) {
			return response()->json([
				'error'   => true,
				'message' => 'Email or Password mismatch',
			], 400);
		}

		// save access token
		$token = $tokenRepository->saveNewToken($user);
		// cache access token for 5 min with user id as value
		$cacheService->insertAccessTokenToCache($token->access_token, $token->user_id, 5);

		return response()->json([
			'user_id'       => $user->id,
			'name'          => $user->name,
			'email'         => $user->email,
			'access_token'  => $token->access_token,
			'refresh_token' => $token->refresh_token,
			'expires_in'    => $token->expires_in->toDateTimeString(),
		], 200);
	}
}
