<?php

namespace iceberg\errorhandler;

class ErrorHandler {

	private static $registered = false;

	public static function register() {
		ob_start();
		
		if (!static::$registered)
			return set_exception_handler(__CLASS__."::exceptionHandler");
		return false;
	}
	
	public static function exceptionHandler($exception) {
		ob_end_clean();
		
		echo $exception->getMessage(), PHP_EOL;
		exit;
	}
	
}