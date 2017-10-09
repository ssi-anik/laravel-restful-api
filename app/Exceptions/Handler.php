<?php namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
	protected $dontReport = [];

	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	public function report (Exception $exception) {
		parent::report($exception);
	}

	public function render ($request, Exception $exception) {
		return $this->formatException($request, $exception);
	}

	private function formatException ($request, Exception $exception) {
		$statusCode = 400;
		$data = [];
		switch (get_class($exception)) {
			case ValidationException::class:
				$statusCode = 422;
				foreach ($exception->errors() as $key => $value) {
					$data[$key] = $value[0];
				}
				break;
			default:
				$data = [$exception->getMessage()];
				break;
		}

		return app(Controller::class)->respondError($data, $statusCode);
	}
}
