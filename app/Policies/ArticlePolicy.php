<?php namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
	use HandlesAuthorization;

	public function __construct () {
		//
	}

	public function delete (User $user, Article $article) {
		// check if user is super admin or not
		// todo: check this.
		// check if user is the article creator
		return $user->id == $article->user_id;
	}
}
