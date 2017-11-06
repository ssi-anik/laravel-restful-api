<?php namespace App\Services;

use Illuminate\Cache\CacheManager;
use Illuminate\Support\Str;

class CacheService
{
	private $cache = null;
	private $accessTokenPrefix = 'AT';
	private $articlePrefix = 'ART';
	private $articleSetPrefix = 'AS';

	public function __construct (CacheManager $cache) {
		$this->cache = $cache;
	}

	private function articleSetKeyBuilder ($page, $perPage, $searchQuery) {
		$searchQuery = str_slug(Str::lower($searchQuery));

		return "{$page}.{$perPage}.{$searchQuery}";
	}

	public function insertAccessTokenToCache ($tokenAsKey, $value, $rememberFor = 5, $prefix = null) {
		$this->cache->setPrefix($prefix ?: $this->accessTokenPrefix);
		$this->cache->put($tokenAsKey, $value, $rememberFor);
	}

	public function checkIfAccessTokenExists ($accessToken, $prefix = null) {
		$this->cache->setPrefix($prefix ?: $this->accessTokenPrefix);

		return $this->cache->get($accessToken);
	}

	public function removeAccessToken (string $accessToken, $prefix = null) {
		$this->cache->setPrefix($prefix ?: $this->accessTokenPrefix);

		return $this->cache->forget($accessToken);
	}

	public function insertArticleToCache ($article, $rememberFor = 5, $prefix = null) {
		$this->cache->setPrefix($prefix ?: $this->articlePrefix);
		$this->cache->put($article->slug, $article, $rememberFor);
	}

	public function checkIfArticleExists ($articleSlug, $prefix = null) {
		$this->cache->setPrefix($prefix ?: $this->articlePrefix);

		return $this->cache->get($articleSlug);
	}

	public function insertArticleChunkToCache ($articles, $page, $perPage, $searchQuery, $rememberFor = 5, $prefix = null) {
		$this->cache->setPrefix($prefix ?: $this->articleSetPrefix);
		$this->cache->put($this->articleSetKeyBuilder($page, $perPage, $searchQuery), $articles, $rememberFor);
	}

	public function getArticleSetFromCache ($page, $perPage, $searchQuery, $prefix = null) {
		$this->cache->setPrefix($prefix ?: $this->articleSetPrefix);

		return $this->cache->get($this->articleSetKeyBuilder($page, $perPage, $searchQuery));
	}

	public function removeArticleFromCache ($articleSlug, $prefix = null) {
		$this->cache->setPrefix($prefix ?: $this->articlePrefix);
		$this->cache->forget($articleSlug);
	}
}