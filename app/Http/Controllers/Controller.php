<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function respondSuccess ($data, $statusCode = 200) {
		return $this->respond([ 'data' => $data ], $statusCode);
	}

	public function respondError ($data, $statusCode = 400) {
		return $this->respond([ 'error' => true, 'causes' => $data ], $statusCode);
	}

	private function respond ($data, $statusCode) {
		return response()->json($data, $statusCode);
	}
}
