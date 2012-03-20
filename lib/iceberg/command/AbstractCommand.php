<?php

namespace iceberg\command;

abstract class AbstractCommand {

	abstract public static function validates($args);
	abstract public static function run($args);

	public static function validateThenRun($args) { 
		if (static::validates($args))
			static::run($args);
	}
	
	public static function exists() { return true; }

}