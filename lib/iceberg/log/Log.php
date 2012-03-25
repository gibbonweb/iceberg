<?php

namespace iceberg\log;

use iceberg\filesystem\FileSystem;

class Log {

	private static $logPath = false;
	
	public static function setLogFile($path) {
		if (!file_exists($path))
			FileSystem::writeFile($path, "");
	
		static::$logPath = $path;
	}
	
	public static function log($name, $message) {
		$message = "[".date(DATE_RFC822)."] $name : $message".PHP_EOL;
		
		FileSystem::writeFile(static::$logPath, $message, true);
	}

}