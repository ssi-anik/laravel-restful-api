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

		if ($article->relationLoaded('user')) {
			$user = [
				'user_id' => $article->user->id,
				'name'    => $article->user->name,
			];
			$data = array_merge($user, $data);
		}

		if ($article->relationLoaded('tags')) {
			$data = array_merge($data, $article->tags->pluck('content', 'id'));
		}

		return $data;
	}

}