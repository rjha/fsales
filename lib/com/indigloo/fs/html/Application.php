<?php

namespace com\indigloo\fs\html {

    use \com\indigloo\Template as Template;
    
    class Application {

        static function getPageTable($pages) {
            if(empty($pages)) { return ""; }
            $html = NULL ;
            $template = "/app/fragments/page/table.tmpl" ;
            $view = new \stdClass;
            $view->pages = $pages ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getSource($selected, $all) {

        }

        static function getNoSource() {

        }

        static function getComment($row) {
            if(empty($row)) { return ""; }
            $html = NULL ;
            $template = "/app/fragments/comment.tmpl" ;
            $view = new \stdClass;
            
            $view->picture = $row["picture"] ;
            $view->link = $row["link"];
            $view->message = $row["message"];

            $view->userName = $row["user_name"];
            $view->comment = $row["comment"];
            $view->profile = sprintf("http://www.facebook.com/profile.php?id=%s",$row["from_id"]) ;

            $html = Template::render($template,$view);
            return $html ;
        }

    }
}

?>
