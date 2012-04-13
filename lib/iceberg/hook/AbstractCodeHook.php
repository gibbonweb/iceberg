<?php

namespace iceberg\hook;

abstract class AbstractCodeHook {

	public static function exists() { return true; }
	public static function prepare($posts) {}
	public static function run($posts) {}

}