<?php

namespace iceberg\hook;

use iceberg\hook\exceptions\HookNotFoundException;

class Hook {

	private static $namespace = false;

	public static function setNamespace($namespace) {
		static::$namespace = $namespace;
	}
	
	public static function call($hook, $pass = false) {
		$path = str_replace("(hook)", ucfirst($hook), static::$namespace);

		$exists = call_user_func("$path::exists");
		if (!$exists)
			return false;

		call_user_func("$path::prepare", $pass);
		call_user_func("$path::run", $pass);
	}

}