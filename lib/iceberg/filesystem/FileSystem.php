<?php

namespace iceberg\filesystem;

use iceberg\filesystem\exceptions\CopySourceDoesNotExistException;
use iceberg\filesystem\exceptions\CannotCreateOutputDestinationException;

class FileSystem {

	public static function writeFile($path, $data, $append = false) {
		static::recursiveMkdir($path);
		
		if ($append)
			$handle = fopen($path, "a");
		else 
			$handle = fopen($path, "w");

		fwrite($handle, $data);
		fclose($handle);
	}
	
	public static function recursiveMkdir($path) {
		$bits = array_slice(explode(DIRECTORY_SEPARATOR, $path), 1);
		$countBits = count($bits);
		
		$checkPath = DIRECTORY_SEPARATOR.$bits[0];
		for ($i = 1; $i < $countBits; $i++) {
			if (!is_dir($checkPath)) {
				$success = @mkdir($checkPath);
				if (!$success)
					throw new CannotCreateOutputDestinationException("Could not create directory \"$checkPath\"");
			}
			$checkPath .= DIRECTORY_SEPARATOR.$bits[$i];
		}
	}
	
	public static function recursiveCopy($src, $dst, $mkdir = false) {
		if (!is_dir($src))
			throw new CopySourceDoesNotExistException("Source \"$src\" does not exist!");
		
		if ($mkdir)
			static::recursiveMkdir($dst);
		
		@mkdir($dst);
		
		$files = array_slice(scandir($src), 2);
		foreach ($files as $file) {
			if (is_dir($src.DIRECTORY_SEPARATOR.$file))
				static::recursiveCopy($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file);
			else
				copy ($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file);
		}
		
	}

}