<?php

require_once __DIR__ . "/Parser.php";

class Config {

    public static function load($path) {
        if (file_exists($path)) 
            return Parser::yaml(file_get_contents($path));
        else
            die("Please enter a valid config file (or create it if it was deleted).\n");
    }

}