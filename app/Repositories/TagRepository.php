<?php namespace App\Repositories;

use App\Models\Tag;
use Carbon\Carbon;

class TagRepository
{
	private $tag;

	public function __construct (Tag $tag) {
		$this->tag = $tag;
	}

	public function storeUnavailableTags ($tagData, $userId) {
		$availableTags = $this->tag->whereIn('content', $tagData->toArray())->pluck('content')->toArray();
		$unavailableTags = $tagData->diff($availableTags)->map(function ($item) use ($userId) {
			$now = Carbon::now()->toDateTimeString();

			return [ 'content' => $item, 'created_at' => $now, 'updated_at' => $now, 'user_id' => $userId ];
		});

		if ($unavailableTags->count()) {
			$this->tag->insert($unavailableTags->toArray());
		}

		return $this->tag->whereIn('content', $tagData->toArray())->get();
	}

	public function checkIfTagExists ($tagSlug) {
		return $this->tag->where('content', $tagSlug)->first();
	}
}