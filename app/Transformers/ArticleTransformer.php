<?php namespace App\Transformers;

class ArticleTransformer extends BaseTransformer
{
	public function transform ($article) {
		$data = [
			'id'         => $article->id,
			'title'      => $article->title,
			'content'    => $article->content,
			'created_at' => $article->created_at->toDateTimeString(),
		];

		if ($article->relationLoaded('tags')) {
			$data = array_merge($data, [ 'tags' => $article->tags->pluck('content') ]);
		}

		if ($article->relationLoaded('user')) {
			$user = [
				'id'   => $article->user->id,
				'name' => $article->user->name,
			];

			$data = array_merge($data, [ 'user' => $user ]);
		}

		return $data;
	}

}