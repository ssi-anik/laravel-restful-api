<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	protected $fillable = [ 'title', 'content' ];

	protected $perPage = 20;

	public function setPerPage ($perPage) {
		// if the value is somehow not falsy
		if ($perPage) {
			parent::setPerPage($perPage);
		}

		return $this;
	}

	public function scopeSearch ($query, $search) {
		if (!$search) {
			return;
		}
		$this->scopeSearchOnTitle($query, $search);
		$this->scopeSearchOnContent($query, $search);
	}

	public function scopeSearchOnTitle ($query, $search) {
		if (!$search) {
			return;
		}

		$query->orWhere('title', 'LIKE', sprintf("%%%s%%", $search));
	}

	public function scopeSearchOnContent ($query, $search) {
		if (!$search) {
			return;
		}
		$query->orWhere('content', 'LIKE', sprintf("%%%s%%", $search));
	}

	public function tags () {
		return $this->belongsToMany(Tag::class);
	}

	public function user () {
		return $this->belongsTo(User::class);
	}
}
