<?php namespace App\Transformers;

use App\Exceptions\TransformerException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use \Illuminate\Support\Collection as SupportCollection;

abstract class BaseTransformer
{
	abstract public function transform ($object);

	public function transformCollection ($collection, $callback = 'transform', $dataKey = 'data', ...$arguments) {
		if (!($collection instanceof Collection) && !($collection instanceof Paginator) && !($collection instanceof SupportCollection)) {
			throw new TransformerException();
		}
		$data = [];
		$results = $this->transformCollectionRaw($collection, $callback, ...$arguments);
		$data = array_merge($data, [ $dataKey => $results->toArray() ]);
		if ($collection instanceof Paginator) {
			$data['paginate'] = [
				'current_page'  => $collection->currentPage(),
				'per_page'      => (int) $collection->perPage(),
				'total_in_page' => $collection->count(),
				'total_page'    => $collection->lastPage(),
				'total'         => $collection->total(),
			];

			if ($collection->nextPageUrl()) {
				$data['paginate']['next_page'] = $collection->nextPageUrl();
			}
			if ($collection->previousPageUrl()) {
				$data['paginate']['previous_page'] = $collection->previousPageUrl();
			}
		}

		return $data;
	}

	public function transformCollectionRaw ($collection, $callback = 'transform', ...$arguments) {
		if (!($collection instanceof Collection) && !($collection instanceof Paginator) && !($collection instanceof SupportCollection)) {
			throw new TransformerException();
		}

		return $collection->map(function ($row) use ($callback, $arguments) {
			return call_user_func_array(array( $this, $callback ), [ $row, $arguments ]);
		});
	}
}