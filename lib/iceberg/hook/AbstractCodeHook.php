<?php

namespace iceberg\hook;

abstract class AbstractShellHook {

	abstract public static function run() {}
	
	public static function exists() { return true; }

}