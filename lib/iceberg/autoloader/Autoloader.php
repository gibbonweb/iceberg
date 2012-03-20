<?php

namespace iceberg\autoloader;

class Autoloader {

	private static $namespaces = array();
	private static $registered = false;
	
	public static function register() {
		if (static::$registered)
			return false;
			
		static::$registered = spl_autoload_register(__CLASS__.'::loadClass');
		return static::$registered;
	}
	
	public static function setNamespace($namespace, $root) {
		static::$namespaces[$namespace] = $root;

		if (!static::$registered)
			static::register();
	}

	public static function loadClass($class) { 
		$pathBits = explode("\\", $class);
		
		if (!isset(static::$namespaces[$pathBits[0]]))
			return false;
		
		$path = static::$namespaces[$pathBits[0]]
			   .DIRECTORY_SEPARATOR
			   .implode(DIRECTORY_SEPARATOR, array_slice($pathBits, 1))
			   .".php";
		
		if (!file_exists($path))
			return false;
			
		include_once $path;
		
	}

}