<?php namespace App\Listeners;

use App\Repositories\TokenRepository;
use App\Services\CacheService;

class RefreshTokenEventListener
{
	private $tokenRepository, $cacheService;

	public function __construct (TokenRepository $tokenRepository, CacheService $cacheService) {
		$this->tokenRepository = $tokenRepository;
		$this->cacheService = $cacheService;
	}

	public function handle ($event) {
		// invalidate the given access token
		$this->tokenRepository->invalidateAccessToken($event->previousAccessToken);
		// invalidate cache from cache.
		$this->cacheService->removeAccessToken($event->previousAccessToken->access_token);
	}
}
