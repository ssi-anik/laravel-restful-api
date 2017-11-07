<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
	public function authorize () {
		return true;
	}

	public function rules () {
		return [
			'title'   => 'max:100|required_without_all:content,tags',
			'content' => 'max:10000|required_without_all:title,tags',
			'tags'    => 'array|max:5|required_without_all:content,title',
			'tags.*'  => 'required|max:20',
		];
	}
}
