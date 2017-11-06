<?php namespace App\Services;

use Illuminate\Cache\CacheManager;

class CacheService
{
	private $cache = null;
	private $accessTokenPrefix = 'AT';

	public function __construct (CacheManager $cache) {
		$this->cache = $cache;
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
		if ($this->checkIfAccessTokenExists($accessToken, $prefix)) {
			$this->cache->forget($accessToken);
		}

		return true;
	}
}