<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefreshTokenRequest extends FormRequest
{
	public function authorize () {
		return true;
	}

	public function rules () {
		return [
			'refresh_token' => 'required',
		];
	}
}
