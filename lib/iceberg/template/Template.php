<?php

namespace iceberg\template;

use iceberg\template\exceptions\TemplateFileNotFoundException;

class Template {

	public static function load($template, $data) {
		
		if (!file_exists($template))
			throw new TemplateFileNotFoundException("Template file \"$template\" not found.");
		
		ob_start();
		$post = &$data;
		
		include $template;

		$compiled = ob_get_contents();
		ob_end_clean();
		
		return $compiled;
	}

}