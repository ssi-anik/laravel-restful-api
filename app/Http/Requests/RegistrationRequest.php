<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
	public function authorize () {
		return true;
	}

	public function rules () {
		return [
			'name'            => 'required|max:30',
			'email'           => 'required|unique:users,email',
			'password'        => 'required|confirmed|min:6',
			'profile_picture' => 'sometimes|mimes:jpeg,bmp,png',
		];
	}

	public function messages () {
		return [];
	}
}
