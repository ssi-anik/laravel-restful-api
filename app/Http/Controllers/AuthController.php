<?php namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
	public function postRegistration (RegistrationRequest $request) {
		$form = [
			'name'     => trim($request->get('name')),
			'email'    => trim($request->get('email')),
			'password' => $request->get('password'),
		];
		// save user
		$user = new User();
		foreach ($form as $field => $item) {
			$user->$field = $item;
		}
		// write request to log file
		Logger::logRequest($request->all(), $request->route()
													->getName());

		// check if profile picture exits
		if ($request->hasFile('profile_picture')) {
			$imageName = sprintf('%s.png', str_random(30));
			$imagePath = "images/{$imageName}";
			$image = Image::make($request->file('profile_picture'));
			$image->resize(30, 30);
			$image->save(storage_path($imagePath));
			$user->profile_picture = $imagePath;
		}
		$user->save();

		// generate access token
		$token = Token::create([
			'user_id'       => $user->id,
			'access_token'  => Logger::generateUniqueString(),
			'refresh_token' => Logger::generateUniqueString(),
			'expires_in'    => Carbon::now()
									 ->addDays(env('TOKEN_VALIDATION_IN_DAYS')),
		]);

		return response()->json([
			'user_id'       => $user->id,
			'name'          => $user->name,
			'email'         => $user->email,
			'access_token'  => $token->access_token,
			'refresh_token' => $token->refresh_token,
			'expires_in'    => $token->expires_in->toDateTimeString(),
		], 201);
	}

	public function postLogin (LoginRequest $request) {
		$isSuccessful = Auth::attempt([
			'email'      => $request->get('email'),
			'password'   => $request->get('password'),
			'deleted_at' => null,
		]);

		if (false === $isSuccessful) {
			return response()->json([
				'error'   => true,
				'message' => 'Email or Password mismatch',
			], 400);
		}

		$user = Auth::user();

		$token = Token::create([
			'user_id'       => $user->id,
			'access_token'  => Logger::generateUniqueString(),
			'refresh_token' => Logger::generateUniqueString(),
			'expires_in'    => Carbon::now()
									 ->addDays(env('TOKEN_VALIDATION_IN_DAYS')),
		]);

		return response()->json([
			'user_id'       => $user->id,
			'name'          => $user->name,
			'email'         => $user->email,
			'access_token'  => $token->access_token,
			'refresh_token' => $token->refresh_token,
			'expires_in'    => $token->expires_in->toDateTimeString(),
		], 201);
	}
}
