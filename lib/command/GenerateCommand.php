<?php

namespace command;

use iceberg\hook\Hook;
use iceberg\parser\Spyc;
use iceberg\config\Config;
use iceberg\parser\MarkdownExtra;
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
		
		$markdown = new MarkdownExtra;
		
		$post = array();
		$post["info"] = Spyc::YAMLLoadString(trim($meta));
		$post["info"]["time"] = filemtime($inputFile);
		$post["text"] = $markdown->transform($postContent);
		$post["hash"] = md5($post["info"]["title"]);
		
		if (!isset($post["info"]["author"]))
			$post["info"]["author"] = Config::getVal("general", "author");
		
		$absoluteAssetPath = Config::getVal("general", "path")
		                    ."/"
		                    .str_replace("(name)", $post["info"]["slug"], Config::getVal("article", "output"));
		$post["text"] = preg_replace('/="assets(.*)"/', "=\"".$absoluteAssetPath."/assets$1\"", $post["text"]);
		
		$postQueryData = $post;
		$postQueryData["data"] = json_encode($post["info"]);
		
		Database::query("DELETE FROM data WHERE hash = '" . $postQueryData["hash"] . "'");
		$postQuery = "INSERT OR IGNORE INTO data (hash, text, data) VALUES (\""
		             .$postQueryData["hash"]
		             ."\", \""
		             .str_replace("\n", '\n', htmlentities($postQueryData["text"]))
		             ."\", \""
		             .htmlentities(addslashes($postQueryData["data"]))."\")";
		Database::query($postQuery);
		
		if (!isset($post["info"]["layout"]))
			throw new ParameterNotFoundException("Required front-matter \"layout\" parameter not found.");
	
		$filesToCompile = array();
	
		$templatePath = $post["info"]["layout"].".twig";
		$templateOutputPath = Config::getVal("general", "output")
		                     .str_replace("(name)", $post["info"]["slug"], Config::getVal("article", "output"))
		                     ."index.html";
	
		$filesToCompile[$templatePath] = ROOT_DIR.$templateOutputPath;
		
		$reloadFilePath = ROOT_DIR.Config::getVal("article", "layout").$post["info"]["layout"].".reload";
		$reloadFilesContent = @file_get_contents($reloadFilePath);
		if (!!$reloadFilesContent) {
			$reloadFileParsed = Spyc::YAMLLoadString(trim($reloadFilesContent));
			foreach ($reloadFileParsed as $template => $output) {
				
				$output = ROOT_DIR.Config::getVal("general", "output").DIRECTORY_SEPARATOR.$output;
			
				if (is_dir(ROOT_DIR.Config::getVal("article", "layout").$template)) {
					FileSystem::recursiveCopy(ROOT_DIR.Config::getVal("article", "layout").$template, $output, true);
				} else {
					$template = "$template.twig";
					$filesToCompile[$template] = $output;
				}

			}
		}
		
		$posts = Database::query("SELECT * FROM data");
		
		$postsClean = array();
		for ($i = 0; $i < count($posts); $i++) {
			if (!($i % 2))
				$postsClean[] = array("text" => str_replace('\n', "\n", html_entity_decode($posts[$i]["text"])),
				                      "info" => get_object_vars(json_decode(stripslashes(html_entity_decode($posts[$i]["data"])))));
		}
		
		$templateArray = array();
		$templateArray["posts"] = $postsClean;
		$templateArray["post"]= end($postsClean);
		$templateArray["general"] = array(
			"path" => Config::getVal("general", "path"),
			"title" => Config::getVal("general", "title")
		);
	
		foreach ($filesToCompile as $template => $output) {
			$templateParsed = Template::load($template, $templateArray);
			FileSystem::writeFile($output, $templateParsed);
		}
	
		$postAssets = ROOT_DIR.str_replace("(name)", $args[0], Config::getVal("article", "input"))."/assets";
		$postAssetsOutput = ROOT_DIR
		                   .Config::getVal("general", "output")."/"
		                   .str_replace("(name)", $post["info"]["slug"], Config::getVal("article", "output"))
		                   ."/assets";

		if (is_dir($postAssets))
			FileSystem::recursiveCopy($postAssets, $postAssetsOutput, true);
		
		echo "-> Successfully created \"" .$args[0]. "\" at \"$templateOutputPath\"", PHP_EOL;
		
		if ($runHook)
			Hook::call("postGenerate", $postsClean);
	}

}