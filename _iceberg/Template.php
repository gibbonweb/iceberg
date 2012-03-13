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
    
}