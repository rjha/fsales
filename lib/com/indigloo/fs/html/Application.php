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

        static function getSource($selectedRow, $allRows) {
            $html = NULL ;
            $template = "/app/fragments/source.tmpl" ;
            $view = new \stdClass;
            $view->allRows = $allRows ;
            $view->selectedRow = $selectedRow;

            $html = Template::render($template,$view);
            return $html ;
        }

        static function getNoSource() {
            $html = NULL ;
            $template = "/app/fragments/no-source.tmpl" ;
            $view = new \stdClass;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getComment($row) {
            if(empty($row)) { return ""; }
            $html = NULL ;
            $template = "/app/fragments/comment.tmpl" ;
            $view = new \stdClass;
            
            $view->picture = $row["picture"] ;
            $view->link = $row["link"];
            $view->message = $row["message"];
            $view->time = $row["created_ts"];

            $view->userName = $row["user_name"];
            $view->comment = $row["comment"];
            $view->profile = sprintf("http://www.facebook.com/profile.php?id=%s",$row["from_id"]) ;

            $html = Template::render($template,$view);
            return $html ;
        }
        /*
        select p.picture, p.link, p.message, 
        c.message as comment, c.from_id, c.user_name, c.created_ts 
        from fs_post p, fs_comment c 
        where c.source_id = '%s' 
        and c.post_id = p.post_id 
        and c.created_ts < 1357632064 
        order by c.created_ts DESC LIMIT 3
        */
    
    }
}

?>
