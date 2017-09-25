<?php namespace App\Services;

use Illuminate\Auth\AuthManager;

class AuthService
{
	private $auth = null;

	public function __construct (AuthManager $auth) {
		$this->auth = $auth;
	}

	public function check ($credentials) {
		return $this->auth->attempt($credentials) ? $this->auth->user() : false;
	}
}