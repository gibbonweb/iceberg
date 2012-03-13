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

    public static function recursiveCopy($src,$dst) { 
	    $dir = opendir($src); 
	    @mkdir($dst); 
	    
	    while(false !== ( $file = readdir($dir)) ) { 
	        if (( $file != '.' ) && ( $file != '..' )) { 
	            if ( is_dir($src . '/' . $file) ) { 
	                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	            else { 
	                copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	        } 
	    } 

	    closedir($dir); 
	} 
    
}