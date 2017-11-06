<?php namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
	protected $responseMessage, $httpStatusCode, $responseCode;

	public function __construct($message = "Exception occurred", $code = 400)
	{
		parent::__construct($message, $code);
		$this->setResponseMessage($message)
			 ->setHttpStatusCode($code);
	}

	public function getCustomResponse()
	{
		return response()->json([
			'message' => $this->getResponseMessage(),
			'error' => isset($this->data) ? $this->data : [],
		], $this->getHttpStatusCode());
	}

	public function getHttpStatusCode()
	{
		return $this->httpStatusCode;
	}

	public function setHttpStatusCode($code)
	{
		$this->httpStatusCode = $code;

		return $this;
	}

	public function getResponseMessage()
	{
		return $this->responseMessage;
	}

	public function setResponseMessage($message)
	{
		$this->responseMessage = $message;

		return $this;
	}
}