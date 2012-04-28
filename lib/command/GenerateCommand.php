<?php

namespace command;

use iceberg\hook\Hook;
use iceberg\parser\Spyc;
use iceberg\config\Config;
use iceberg\parser\Markdown;
use iceberg\template\Template;
use iceberg\database\Database;
use iceberg\filesystem\FileSystem;
use iceberg\command\AbstractCommand;
use iceberg\command\exceptions\ParameterNotFoundException;
use iceberg\command\exceptions\InputFileNotFoundException;

class GenerateCommand extends AbstractCommand {

	private static $path = false;
	private static $sqlStructure = "lib/command/structure.sql";

	public static function run($args = array(), $runHook = true) {
	
		if(!isset($args[0]))
			throw new ParameterNotFoundException("Required parameter \"name\" not found.");
		
		if (!file_exists(ROOT_DIR.static::$sqlStructure))
			throw new InputFileNotFoundException("Database structure file not found.");
	
		$sqlStructureContent = file_get_contents(ROOT_DIR.static::$sqlStructure);
		Database::load($sqlStructureContent, ROOT_DIR.Config::getVal("general", "database"));
		
		if (in_array("--no-hook", $args))
			$runHook = false;
		
		if ($args[0] == "--all") {
			$posts = scandir(ROOT_DIR.str_replace("(name)", "", Config::getVal("article", "input")));
			foreach ($posts as $post) {
				if ($post[0] != ".")
					static::run(array($post), $runHook);
			}
			return 0;
		}
		
		if ($runHook)
			Hook::call("preGenerate");
		
		$inputFile = ROOT_DIR.str_replace("(name)", $args[0], Config::getVal("article", "input"))."/".$args[0].".md";
		$inputFileData = @file_get_contents($inputFile);
		
		if (!$inputFileData)
			throw new InputFileNotFoundException("Required input file \"$inputFile\" not found.");
		
		list($meta, $postContent) = array_slice(explode("-----", $inputFileData), 1);
		
		$markdown = new Markdown;
		
		$post = array();
		$post["data"] = Spyc::YAMLLoadString(trim($meta));
		$post["data"]["time"] = filemtime($inputFile);
		$post["data"]["path"] = Config::getVal("general", "path");
		$post["text"] = $markdown->transform($postContent);
		$post["hash"] = md5($post["data"]["title"]);
		
		if (!isset($post["data"]["author"]))
			$post["data"]["author"] = Config::getVal("general", "author");
		
		$postQueryData = $post;
		$postQueryData["data"] = json_encode($post["data"]);
		
		Database::query("DELETE FROM data WHERE hash = '" . $postQueryData["hash"] . "'");
		$postQuery = "INSERT OR IGNORE INTO data (hash, text, data) VALUES (\""
		             .$postQueryData["hash"]
		             ."\", \""
		             .str_replace("\n", "", htmlentities($postQueryData["text"]))
		             ."\", \""
		             .htmlentities(addslashes($postQueryData["data"]))."\")";
		Database::query($postQuery);
		
		if (!isset($post["data"]["layout"]))
			throw new ParameterNotFoundException("Required front-matter \"layout\" parameter not found.");
	
		$filesToCompile = array();
	
		$templatePath = ROOT_DIR.str_replace("(layout)", $post["data"]["layout"], Config::getVal("article", "layouts"));
		$templateOutputPath = str_replace("(name)", $post["data"]["slug"], Config::getVal("article", "output"))."/index.html";
	
		$filesToCompile[$templatePath] = ROOT_DIR.$templateOutputPath;
		
		$reloadFilePath = ROOT_DIR.str_replace("(layout)", $post["data"]["layout"], Config::getVal("article", "reloads"));
		$reloadFilesContent = @file_get_contents($reloadFilePath);
		if (!!$reloadFilesContent) {
			$reloadFileParsed = Spyc::YAMLLoadString(trim($reloadFilesContent));
			foreach ($reloadFileParsed as $template => $output) {
				
				$output = ROOT_DIR.Config::getVal("general", "output").DIRECTORY_SEPARATOR.$output;
			
				if (is_dir(ROOT_DIR.Config::getVal("general", "layout").DIRECTORY_SEPARATOR.$template)) {
					FileSystem::recursiveCopy(ROOT_DIR.Config::getVal("general", "layout").DIRECTORY_SEPARATOR.$template, $output, true);
				} else {
					$template = ROOT_DIR.str_replace("(layout)", $template, Config::getVal("article", "layouts"));
					$filesToCompile[$template] = $output;
				}

			}
		}
		
		$posts = Database::query("SELECT * FROM data");
		$postsClean = array();
		for ($i = 0; $i < count($posts); $i++) {
			if (!($i % 2))
				$postsClean[] = array("text" => html_entity_decode($posts[$i]["text"]),
				                      "data" => get_object_vars(json_decode(stripslashes(html_entity_decode($posts[$i]["data"])))));
		}
	
		foreach ($filesToCompile as $template => $output) {
			$templateParsed = Template::load($template, $postsClean);
			FileSystem::writeFile($output, $templateParsed);
		}
	
		$postAssets = ROOT_DIR.str_replace("(name)", $args[0], Config::getVal("article", "input"))."/assets";
		$postAssetsOutput = ROOT_DIR.str_replace("(name)", $args[0], Config::getVal("article", "output"))."/assets";

		if (is_dir($postAssets))
			FileSystem::recursiveCopy($postAssets, $postAssetsOutput, true);
		
		echo "-> Successfully created \"" .$args[0]. "\" at \"$templateOutputPath\"", PHP_EOL;
		
		if ($runHook)
			Hook::call("postGenerate", $postsClean);
	}

}