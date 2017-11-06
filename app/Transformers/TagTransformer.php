<?php namespace App\Transformers;

class TagTransformer extends BaseTransformer
{
	public function transform ($article) {
		return [
			'id'      => (int) $article->id,
			'content' => $article->content,
		];
	}

}