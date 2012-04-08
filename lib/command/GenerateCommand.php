<?php

namespace command;

use iceberg\hook\Hook;
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

	public static function run($args = array()) {
	
		if(!isset($args[0]))
			throw new ParameterNotFoundException("Required parameter \"name\" not found.");
		
		if ($args[0] == "--all") {
			$posts = scandir(ROOT_DIR.str_replace("(name)", "", Config::getVal("article", "input")));
			foreach ($posts as $post) {
				if ($post[0] != ".")
					static::run(array($post));
			}
			return 0;
		}
		
		Hook::call("preGenerate");
		
		$inputFile = ROOT_DIR.str_replace("(name)", $args[0], Config::getVal("article", "input"))."/".$args[0].".md";
		$inputFileData = @file_get_contents($inputFile);
		
		if (!$inputFileData)
			throw new InputFileNotFoundException("Required input file \"$inputFile\" not found.");
		
		list($meta, $postContent) = array_slice(explode("-----", $inputFileData), 1);
		
		$markdown = new Markdown;
		
		$post = array();
		$post["info"] = Spyc::YAMLLoadString(trim($meta));
		$post["info"]["time"] = filemtime($inputFile);
		$post["info"]["path"] = Config::getVal("general", "path");
		$post["content"] = $markdown->transform($postContent);
		
		if (!isset($post["info"]["layout"]))
			throw new ParameterNotFoundException("Required front-matter \"layout\" parameter not found.");
	
	
		$filesToCompile = array();
	
		$templatePath = ROOT_DIR.str_replace("(layout)", $post["info"]["layout"], Config::getVal("article", "layouts"));
		$templateOutputPath = ROOT_DIR.str_replace("(name)", $args[0], Config::getVal("article", "output"))."/index.html";
	
		$filesToCompile[$templatePath] = $templateOutputPath;
		
		$reloadFilePath = ROOT_DIR.str_replace("(layout)", $post["info"]["layout"], Config::getVal("article", "reloads"));
		$reloadFilesContent = @file_get_contents($reloadFilePath);
		if (!!$reloadFilesContent) {
			$reloadFileParsed = Spyc::YAMLLoadString(trim($reloadFilesContent));
			foreach ($reloadFileParsed as $template => $output) {
				$output = ROOT_DIR.Config::getVal("general", "output").DIRECTORY_SEPARATOR.$output;
				$template = ROOT_DIR.str_replace("(layout)", $template, Config::getVal("article", "layouts"));
				
				$filesToCompile[$template] = $output;
			}
		}
	
		foreach ($filesToCompile as $template => $output) {
			$templateParsed = Template::load($template, $post);
			FileSystem::writeFile($output, $templateParsed);
		}
	
		$postAssets = ROOT_DIR.str_replace("(name)", $args[0], Config::getVal("article", "input"))."/assets";
		$postAssetsOutput = ROOT_DIR.str_replace("(name)", $args[0], Config::getVal("article", "output"))."/assets";
			
		if (is_dir($postAssets))
			FileSystem::recursiveCopy($postAssets, $postAssetsOutput, true);
		
		echo "-> Successfully created \"" .$args[0]. "\" at \"$templateOutputPath\".", PHP_EOL;
		
		Hook::call("postGenerate");
	}

}