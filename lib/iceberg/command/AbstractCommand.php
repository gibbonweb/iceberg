<?php

namespace iceberg\command;

abstract class AbstractCommand {

	abstract public static function run($args);
	
	public static function exists() { return true; }

}