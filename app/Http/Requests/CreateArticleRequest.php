<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateArticleRequest extends FormRequest
{
	public function authorize () {
		return true;
	}

	public function rules () {
		return [
			'title'   => 'required|max:100',
			'content' => 'required|max:10000',
			'tags'    => 'required|array|max:5',
			'tags.*'  => 'required|max:20',
		];
	}
}
