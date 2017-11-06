<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Token extends Model
{
	use SoftDeletes;

	protected $table = 'tokens';

	protected $fillable = [
		'access_token',
		'refresh_token',
		'user_id',
		'expires_in',
	];

	public function user () {
		return $this->belongsTo(User::class);
	}
}
