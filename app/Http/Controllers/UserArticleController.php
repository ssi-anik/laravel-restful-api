<?php namespace App\Http\Controllers;

use App\Repositories\ArticleRepository;
use App\Repositories\UserRepository;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Request;

class UserArticleController extends Controller
{
	public function index (Request $request, ArticleRepository $articleRepository, UserRepository $userRepository, ArticleTransformer $transformer, $userId) {
		$user = $userRepository->checkIfUserExists($userId);
		if ( !$user) {
			return $this->respondError([ 'user' => 'Not found!' ], 404);
		}

		$perPage = $request->get('per_page', 0);
		if (0 > $perPage || 100 < $perPage) {
			$perPage = 0;
		}

		$searchQuery = $request->get('query');
		$userArticles = $articleRepository->getChunkOfArticlesByUser($userId, $perPage, $searchQuery);
		$transformedArticles = $transformer->transformCollection($userArticles, 'transform', 'articles');

		return $this->respondSuccess($transformedArticles);
	}
}