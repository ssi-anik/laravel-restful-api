<?php namespace App\Helpers;

class Logger
{
	public static function logRequest (array $request, $name) {
		$output = json_encode([ $name => $request ]) . PHP_EOL;
		$file = fopen(storage_path('logs/requests.log'), "a+");
		fwrite($file, $output);
		fclose($file);
	}

	public static function generateUniqueString () {
		return hash("sha256", str_random(30));
	}
}