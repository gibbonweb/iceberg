<?php

namespace iceberg\template;

use iceberg\config\Config;

class Template {

	public static function load($template, $data) {
		ob_start();
		
		$post = &$data;
		
		include ROOT_DIR
			   .DIRECTORY_SEPARATOR
			   .str_replace("(layout)", $template, Config::getVal("article", "layouts"));

		$compiled = ob_get_contents();
		        
		ob_end_clean();
		return $compiled;
	}

}