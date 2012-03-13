<?php

require_once __DIR__ . "/Parsers/Markdown.php";
require_once __DIR__ . "/Parsers/Spyc.php";

class Parser {

    public static function markdown($data) {
        $markdown = new Markdown();
        return $markdown->transform($data);
    }
    
    public static function yaml($data) {
        return Spyc::YAMLLoadString($data);
    }

}