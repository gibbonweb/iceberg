<?php

namespace iceberg\hook;

use iceberg\hook\exceptions\HookDirectoryNotFoundException;

abstract class AbstractShellHook {

	protected static $command;
	protected static $path;
	
	public static function prepare($posts) { }
	public static function exists() { return true; }
	
	public static function run() {

		if (!is_dir(static::$path))
			throw new HookDirectoryNotFoundException("Hook path \"" .static::$path. "\" is not a directory.");
		chdir(static::$path);
		
		if (!is_array(static::$command))
			static::$command = array(static::$command);
		
		foreach (static::$command as $command)
			shell_exec($command." 1>/dev/null 2>&1");

	}

}