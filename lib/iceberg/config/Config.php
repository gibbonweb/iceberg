<?php

namespace iceberg\config;

use iceberg\parser\Spyc;

use iceberg\config\exceptions\ConfigFileNotFoundException;
use iceberg\config\exceptions\InvalidConfigFileException;
use iceberg\config\exceptions\InvalidConfigFileTypeException;
use iceberg\config\exceptions\UnknownConfigValueException;

class Config {

	CONST TYPE_YAML = 1;
	CONST TYPE_INI = 2;

	private static $values = array();

	public static function loadFromArray($array) {
		static::$values = array_merge(static::$values, $array);
	}

	public static function loadFromFile($type, $path) {
		if (!file_exists($path))
			throw new ConfigFileNotFoundException("Config file '$path' not found.");

		$configData = file_get_contents($path);
		
		switch($type) {
			case static::TYPE_INI:
				$parsedConfig = parse_ini_string($configData, true);
				break;
			
			case static::TYPE_YAML:
				$parsedConfig = Spyc::YAMLLoadString($configData);
				break;

			default:
				throw new InvalidConfigFileTypeException("Invalid config file type \"$type\".");
				break;
		}
		
		if (!$parsedConfig)
			throw new InvalidConfigFileException("Invalid or corrupted config file.");
		
		static::$values = array_merge(static::$values, $parsedConfig);
	}

	public static function setVal($section, $key, $val) {
		if (!isset(static::$values[$section]))
			static::$values[$section] = array();
	
		static::$values[$section][$key] = $val;
	}
	
	public static function getVal($section, $key) {
		if (!isset(static::$values[$section][$key]))
			throw new UnknownConfigValueException("Unknown config value \"$section.$key\".");
	
		return static::$values[$section][$key];
	}

}