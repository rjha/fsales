<?php

namespace com\indigloo\fs\html {

    use \com\indigloo\Template as Template;
    
    class Page {

        static function getTable($pages) {
            if(empty($pages)) { return ""; }
            $html = NULL ;
            $template = '/app/fragments/page/table.tmpl' ;
            $view = new \stdClass;
            $view->pages = $pages ;
            $html = Template::render($template,$view);
            return $html ;
        }
    }
}

?>
