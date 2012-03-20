<?php

namespace iceberg;

use Exception;
use iceberg\command\AbstractCommand;

class GenerateCommand extends AbstractCommand {

	public static function validates($args) {
		
		if (!isset($args[0]))
			throw new Exception("First parameter \"name\" not found.");

		return true;
	}
	
	public static function run($args) {
		echo "It runs!";
	}

}