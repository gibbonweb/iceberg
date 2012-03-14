<?php

require_once __DIR__ . "/Parser.php";
require_once __DIR__ . "/Config.php";
require_once __DIR__ . "/Template.php";

class Iceberg {

    public function __construct($configPath) {
        $this->config = Config::load($configPath);
        date_default_timezone_set($this->config["general"]["timezone"]);
    }

    public function generate($name = false, $force = false) {
        if (!$name) die("Please enter the name of an article to compile.\n");
		
		$articlePath = str_replace("(name)", $name, $this->config["article"]["input"]);
	    $article = explode("-----", file_get_contents($articlePath));

	    $post = array();
        $post["info"] = Parser::yaml($article[1]);
        $post["info"]["time"] = filemtime($articlePath);
        $post["content"] = Parser::markdown($article[2]);
        $post["general"] = array("path" => $this->config["general"]["path"]);

        $templatePath = str_replace("(layout)", $post["info"]["layout"], $this->config["article"]["layouts"]);
        $templateOutputPath = str_replace("(name)", $name, $this->config["article"]["output"]);

        $compiledTemplate = Template::toHTML($templatePath, $post);

        Template::writeFile($templateOutputPath, $compiledTemplate);

		echo "-> $name successfully generated at $templateOutputPath\n";
		
    }
    
}