<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	protected $fillable = [ 'title', 'content' ];

	public function tags () {
		return $this->belongsToMany(Tag::class);
	}

	public function user () {
		return $this->belongsTo(User::class);
	}
}
