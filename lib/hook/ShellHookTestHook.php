<?php

namespace hook;

use iceberg\hook\AbstractShellHook;


class ShellHookTestHook extends AbstractShellHook {

	protected static $path = "";
	protected static $command = "mkdir folderCreatedByHook";
	
	public static function prepare() {
		static::$path = ROOT_DIR."output";
	}

}