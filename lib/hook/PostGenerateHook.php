<?php

namespace hook;

use iceberg\hook\AbstractShellHook;

class PostGenerateHook extends AbstractShellHook {

	protected static $path = "";
	protected static $command = "mkdir folderCreatedByShellHook";
	
	public static function prepare() {
		static::$path = ROOT_DIR."output";
	}

}