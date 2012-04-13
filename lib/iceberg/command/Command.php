<?php

namespace iceberg\command;

use iceberg\command\exceptions\CommandNotFoundException;

class Command {

	public static $namespace = array();

	public static function setNamespace($namespace) {
		static::$namespace = $namespace;
	}
	
	public static function load(&$args) {
		$path = str_replace("(command)", ucfirst($args[1]), static::$namespace);
		$passArgument = array_slice($args, 2);
		
		$exists = call_user_func("$path::exists", $passArgument);
		if (!$exists)
			throw new CommandNotFoundException("Command \"".$args[1]."\" not found.");

		call_user_func("$path::run", $passArgument);
	}

}