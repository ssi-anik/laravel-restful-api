<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected function respondSuccess ($data, $statusCode = 200) {
		return $this->respond([ 'data' => $data ], $statusCode);
	}

	protected function respondError ($data, $statusCode = 400) {
		return $this->respond([ 'error' => true, 'data' => $data ], $statusCode);
	}

	private function respond ($data, $statusCode) {
		return response()->json($data, $statusCode);
	}
}
