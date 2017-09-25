<?php namespace App\Services;

use Illuminate\Cache\CacheManager;

class CacheService
{
	private $cache = null;

	public function __construct (CacheManager $cache, $prefix = 'LRAPI') {
		$this->cache = $cache;
	}

	public function insertAccessTokenToCache ($tokenAsKey, $value, $rememberFor = 5, $prefix='AT') {
		$this->cache->setPrefix($prefix);
		return $this->cache->put($tokenAsKey, $value, $rememberFor);
	}
}