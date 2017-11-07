<?php namespace App\Http\Controllers;

use App\Http\Requests\CreateArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Services\CacheService;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
	public function index (ArticleRepository $articleRepository, ArticleTransformer $articleTransformer, Request $request, CacheService $cacheService) {
		$perPage = $request->get('per_page', 0);
		if (0 > $perPage || 100 < $perPage) {
			$perPage = 0;
		}
		$page = $request->get('page') ?: 1;

		$searchQuery = $request->get('query');
		// check if the articles for the page ( assuming first page as 1, with same pagination & searchQuery
		if (!($articles = $cacheService->getArticleSetFromCache($page, $perPage, $searchQuery))) {
			$articles = $articleRepository->getChunkOfArticles($perPage, $searchQuery);
			// if there is nothing then there will be nothing to insert
			if ($articles->count()) {
				$cacheService->insertArticleChunkToCache($articles, $page, $perPage, $searchQuery);
			}
		}

		return $this->respondSuccess($articleTransformer->transformCollection($articles, 'transform', 'articles'));
	}

	public function store (CreateArticleRequest $request, ArticleRepository $articleRepository, TagRepository $tagRepository, ArticleTransformer $transformer, CacheService $cacheService) {
		$articleData = [ 'title' => $request->get('title'), 'content' => $request->get('content') ];
		// lower the contents
		// then generate slug
		$tagData = collect($request->get('tags'))->map(function ($item) {
			return Str::lower($item);
		})->map(function ($item) {
			return str_slug($item);
		});

		try {
			$article = app('db')->transaction(function () use ($articleData, $articleRepository, $tagRepository, $tagData) {
				$userId = auth()->user()->id;
				$article = $articleRepository->storeArticle($articleData, $userId);
				$tags = $tagRepository->storeUnavailableTags($tagData, $userId);
				$articleRepository->insertArticleTagToPivot($article, $tags);

				return $article;
			});
		} catch (\Exception $exception) {
			throw new \Exception("Cannot create article.");
		}

		$cacheService->insertArticleToCache($article, 10);

		return $this->respondSuccess($transformer->transform($article), 201);
	}

	public function show (ArticleTransformer $articleTransformer, ArticleRepository $articleRepository, CacheService $cacheService, $slug) {
		// check if article is in the cache or not
		if ($article = $cacheService->checkIfArticleExists($slug)) {
			// relations are not loaded
			$article->load([ 'user', 'tags' ]);
		} else {
			// load from database
			$article = $articleRepository->fetchAnArticleBySlug($slug, [ 'user', 'tags' ]);
			// no article found
			if (!$article) {
				return $this->respondError([ 'article' => 'Not found!' ], 404);
			}
			// just only to save the the model, not relations
			$cacheService->insertArticleToCache($article->fresh());
		}

		// return the article
		return $this->respondSuccess($articleTransformer->transform($article));
	}

	public function update (UpdateArticleRequest $request, ArticleRepository $articleRepository, TagRepository $tagRepository, CacheService $cacheService, ArticleTransformer $articleTransformer, $slug) {
		$article = $cacheService->checkIfArticleExists($slug) ?: $articleRepository->fetchAnArticleBySlug($slug);
		if (!$article) {
			return $this->respondError([ 'article' => 'Not found!' ], 404);
		}

		if (!auth()->user()->can('update', $article)) {
			return $this->respondError([ 'permission' => "You don't have authorization to delete the article." ], 403);
		}

		// as the article can be updated, remove from the cache
		$cacheService->removeArticleFromCache($article->slug);

		// needed to load tags to check if there is any update
		if (!$article->relationLoaded('tags')) {
			$article->load('tags');
		}

		$sentData = [];
		$sentTags = collect();
		if ($request->has('title')) {
			$sentData['title'] = $request->get('title');
		}

		if ($request->has('content')) {
			$sentData['content'] = $request->get('content');
		}

		if ($request->has('tags')) {
			$sentTags = collect($request->get('tags'))->map(function ($item) {
				return Str::lower($item);
			})->map(function ($item) {
				return str_slug($item);
			});
		}
		// check if it is an identical operation or not
		$changeInArticle = [];
		// new different tags to insert
		$changeInTags = [];

		if ($sentData) {
			foreach ($sentData as $key => $value) {
				if ($article->{$key} != $value) {
					$changeInArticle[$key] = $value;
				}
			}
		}

		if ($sentTags) {
			$changeInTags = $sentTags->union($article->tags->pluck('content'));
		}

		if ($changeInTags->count() == 0 && !$changeInArticle) {
			throw new \Exception("Identical update operation is not valid.");
		}

		try {
			$article = app('db')->transaction(function () use ($article, $changeInArticle, $changeInTags, $sentTags, $articleRepository, $tagRepository) {
				if ($changeInArticle) {
					$article = $articleRepository->updateArticleChange($article, $changeInArticle);
				}

				if ($changeInTags->count()) {
					$newTags = $tagRepository->storeUnavailableTags($sentTags, auth()->user()->id);
					if ($newTags->count()) {
						$articleRepository->updateArticleTagsToPivotTable($article, $newTags);
					}
				}

				return $article;
			});
			$article->load('tags');
		} catch (\Exception $exception) {
			throw new \Exception("Cannot create article.");
		}

		// insert the new article to cache
		$cacheService->insertArticleToCache($article);

		return $this->respondSuccess($articleTransformer->transform($article));
	}

	public function destroy (ArticleRepository $articleRepository, CacheService $cacheService, $slug) {
		$article = $articleRepository->fetchAnArticleBySlug($slug);
		if (!$article) {
			return $this->respondError([ 'article' => 'Not found!' ], 404);
		}
		if (!auth()->user()->can('delete', $article)) {
			return $this->respondError([ 'permission' => "You don't have authorization to delete the article." ], 403);
		}
		$articleRepository->deleteAnArticle($article);
		// remove from cache
		$cacheService->removeArticleFromCache($article->slug);
		// remove previously built cache
		$cacheService->flushArticleSetFromCache();

		return $this->respondSuccess([ 'article' => $article->id ]);
	}
}
