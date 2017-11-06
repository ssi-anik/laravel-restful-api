<?php namespace App\Repositories;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleRepository
{
	private $article;

	public function __construct (Article $article) {
		$this->article = $article;
	}

	public function storeArticle ($articleData, $userId) {
		$article = $this->article->newInstance();
		foreach ($articleData as $key => $value) {
			$article->{$key} = $value;
		}
		$article->user_id = $userId;
		$article->save();

		return $article;
	}

	public function insertArticleTagToPivot (Article $article, Collection $tags) {
		return $article->tags()->attach($tags);
	}

	public function fetchAnArticleById ($id) {
		return $this->article->with('user', 'tags')->find($id);
	}
}