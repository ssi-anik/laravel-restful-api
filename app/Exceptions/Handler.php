<?php namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
			case NotFoundHttpException::class:
				$statusCode = 404;
				$data = [ 'route' => 'Invalid URL.' ];
				break;
			case ValidationException::class:
				$statusCode = 422;
				foreach ($exception->errors() as $key => $value) {
					$data[$key] = $value[0];
				}
				break;
			case AuthenticationException::class:
				$statusCode = 401;
				$data = [ 'access' => 'Unauthenticated' ];
				break;
			case UnauthorizedException::class:
				$statusCode = 403;
				$data = [ 'access' => 'Unauthorized access' ];
				break;
			default:
				$data = [ 'data' => $exception->getMessage() ];
				break;
		}

		return app(Controller::class)->respondError($data, $statusCode);
	}
}
