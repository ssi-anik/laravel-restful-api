<?php namespace App\Http\Controllers;

use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Request;

class TagArticleController extends Controller
{
	public function index (Request $request, ArticleRepository $articleRepository, TagRepository $tagRepository, ArticleTransformer $transformer, $tagSlug) {
		$tag = $tagRepository->checkIfTagExists($tagSlug);
		if ( !$tag) {
			return $this->respondError([ 'tag' => 'Not found!' ], 404);
		}

		$perPage = $request->get('per_page', 0);
		if (0 > $perPage || 100 < $perPage) {
			$perPage = 0;
		}

		$searchQuery = $request->get('query');
		$userArticles = $articleRepository->getChunkOfArticlesByTag($tag->id, $perPage, $searchQuery);
		$transformedArticles = $transformer->transformCollection($userArticles, 'transform', 'articles');

		return $this->respondSuccess($transformedArticles);
	}
}
