<?php

require_once __DIR__ . "/Parser.php";
require_once __DIR__ . "/Config.php";
require_once __DIR__ . "/Template.php";

class Iceberg {
    
    public function __construct($configPath) {
        $this->config = Config::load($configPath);
    }
    
    public function generate($name = false) {
        if (!$name) die("Please enter the name of an article to compile.\n");

        date_default_timezone_set($this->config["date"]["timezone"]);
        
        $postFolder = str_replace("(name)", $name, $this->config["posts"]["path"]);
        $postPath = str_replace("(name)", $name, $this->config["posts"]["file"]);
        $data = preg_split("/-----/", file_get_contents($postPath));
        
        $post = array();
        $post["info"] = Parser::yaml($data[1]);
        $post["info"]["time"] = date($this->config["date"]["format"], filemtime($postPath));
        $post["content"] = Parser::markdown($data[2]);
        
        $templatePath = str_replace("(template)", $post["info"]["layout"], $this->config["templates"]["path"]);
        $compiled = Template::toHTML($templatePath, $post);
        
        $outputPath = str_replace("(name)", $name, $this->config["posts"]["output"]);
        mkdir($outputPath, 0755);
        
        Template::recursiveCopy($postFolder . "/assets", $outputPath . "/assets");

        $handle = fopen($outputPath."/index.html", "w");
        fwrite($handle, $compiled);
        fclose($handle);


        
        echo "$name successfully created at $outputPath\n";

    }
    
}