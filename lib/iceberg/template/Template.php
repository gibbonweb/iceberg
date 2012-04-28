<?php

namespace iceberg\template;

use iceberg\config\Config;

class Template {

	public static function load($template, $data) {
		
		$loader = new \Twig_Loader_Filesystem(ROOT_DIR.Config::getVal("article", "layout"));
		$twig = new \Twig_Environment($loader);
		
		return $twig->render($template, $data);

	}

}