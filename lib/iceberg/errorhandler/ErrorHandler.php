<?php

namespace iceberg\errorhandler;

use iceberg\log\Log;

class ErrorHandler {

	private static $registered = false;

	public static function register() {
		if (!static::$registered)
			return set_exception_handler(__CLASS__."::exceptionHandler");
		return false;
	}
	
	public static function exceptionHandler($exception) {
		Log::log(get_class($exception), $exception->getMessage());

		echo $exception->getMessage(), PHP_EOL;
		exit;
	}
	
}