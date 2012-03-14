<?php

class Template {
    
    public static function toHTML($template, $data) {
        ob_start();
        
        $post = &$data;
        include $template;
        $compiled = ob_get_contents();
        
        ob_end_clean();
        return $compiled;
    }
    
    public static function writeFile($path, $data) {
        $bits = explode("/", $path);
        $countBits = count($bits);
        
        $checkPath = $bits[0];
        for ($i = 1; $i < $countBits; $i++) {
            if (!is_dir($checkPath)) mkdir($checkPath);
            $checkPath .= "/$bits[$i]";
        }
        
        $handle = fopen($path, "w");
        fwrite($handle, $data);
        fclose($handle);
        
    }

    public static function recursiveCopy($src, $dst) { 
	    $dir = opendir($src); 
	    @mkdir($dst); 
	    
	    while(false !== ($file = readdir($dir))) { 
	        if (($file != '.') && ($file != '..')) { 
	            if ( is_dir($src . '/' . $file) ) {
	                self::recursiveCopy($src . '/' . $file, $dst . '/' . $file); 
	            } else {
	                copy($src . '/' . $file, $dst . '/' . $file); 
	            }
	        } 
	    } 

	    closedir($dir); 
	} 
    
}