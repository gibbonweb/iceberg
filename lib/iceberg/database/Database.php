<?php

namespace iceberg\database;

use SQLite3;
use iceberg\database\exceptions\NoDatabaseLoadedException;

class Database {

	public static $database = false;

	public static function load($structure, $path) {
		static::$database = new SQLite3($path);
		static::query($structure);
	}
	
	public static function query($query) {
		if (!static::$database)
			throw new NoDatabaseLoadedException("No database file loaded before query.");

		$queryResult = static::$database->query($query);
		$result = array();
		
		while ($row = $queryResult->fetchArray(SQLITE3_ASSOC)) {
			$result[] = $row;
		}
		
		return $result;
	}

};