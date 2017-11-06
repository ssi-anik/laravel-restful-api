<?php namespace App\Http\Controllers;

use App\Http\Requests\CreateArticleRequest;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Transformers\ArticleTransformer;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
	public function index () {
		//
	}

	public function store (CreateArticleRequest $request, ArticleRepository $articleRepository, TagRepository $tagRepository, ArticleTransformer $transformer) {
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

		return $this->respondSuccess($transformer->transform($article), 201);
	}

	public function show ($id) {
		//
	}

	public function edit ($id) {
		//
	}

	public function update (Request $request, $id) {
		//
	}

	public function destroy ($id) {
		//
	}
}
