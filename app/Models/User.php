<?php namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use Notifiable;

	protected $fillable = [
		'name',
		'email',
		'password',
		'profile_picture',
	];

	protected $hidden = [
		'password',
	];

	public function setPasswordAttribute ($password) {
		$this->attributes['password'] = bcrypt($password);
	}
}
