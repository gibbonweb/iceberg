<?php

namespace iceberg;

use iceberg\parser\Spyc;
use iceberg\config\Config;
use iceberg\parser\Markdown;
use iceberg\template\Template;
use iceberg\filesystem\FileSystem;
use iceberg\command\AbstractCommand;
use iceberg\command\exceptions\ParameterNotFoundException;
use iceberg\command\exceptions\InputFileNotFoundException;

class GenerateCommand extends AbstractCommand {

	private static $path = false;

	public static function validates($args) {
		
		if (!isset($args[0]))
			throw new ParameterNotFoundException("First parameter \"name\" not found.");
		
		static::$path = ROOT_DIR
					   .DIRECTORY_SEPARATOR
					   .str_replace("(name)", $args[0], Config::getVal("article", "input"))
					   .DIRECTORY_SEPARATOR;
		
		if (!file_exists(static::$path.$args[0].".md"))
			throw new InputFileNotFoundException("File \"" . static::$path.$args[0].".md\" not found.");

		return true;
	}
	
	public static function run($args) {
		$articlePath = static::$path.$args[0].".md";
		$articleAssets = static::$path."assets";
		$articleOutputPath = ROOT_DIR
					   		.DIRECTORY_SEPARATOR
					   		.str_replace("(name)", $args[0], Config::getVal("article", "output"))
					   		.DIRECTORY_SEPARATOR;
		
		$articleContent = file_get_contents($articlePath);
		list($metaData, $postHTML) = array_slice(explode(Config::getVal("article", "delimiter"), $articleContent), 1);
		
		$markdown = new Markdown;
		
		$post = array();
		$post["info"] = Spyc::YAMLLoadString(trim($metaData));
		$post["info"]["time"] = filemtime($articlePath);
		$post["info"]["path"] = Config::getVal("general", "path");
		$post["content"] = $markdown->transform($postHTML);
		
		$parsedTemplate = Template::load($post["info"]["layout"], $post);
		FileSystem::writeFile($articleOutputPath."index.html", $parsedTemplate);
		
		if (is_dir($articleAssets))
			FileSystem::recursiveCopy($articleAssets, $articleOutputPath."assets", true);
		
		echo "-> Successfully created " .$args[0]. " at ". str_replace(ROOT_DIR, "", $articleOutputPath) .".";
	
	}

}