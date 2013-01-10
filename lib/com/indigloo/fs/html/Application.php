<?php

namespace com\indigloo\fs\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;
    
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

        static function getNoComment() {
            $html = NULL ;
            $template = "/app/fragments/no-comment.tmpl" ;
            $view = new \stdClass;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getComment($row, $options=NULL) {

            if(empty($row)) { return ""; }
            $html = NULL ;

            $defaults = array( "invoice" => true );
            $settings = Util::getSettings($options,$defaults);

            $template = "/app/fragments/comment.tmpl" ;
            $view = new \stdClass;
            
            $view->commentId = $row["comment_id"];
            $view->picture = $row["picture"] ;
            $view->link = $row["link"];
            $view->post_text = $row["post_text"];

            //unix timestamp to actual date
            $view->time = date("d-M h:i A",$row["created_ts"]);
            $view->userName = $row["user_name"];
            $view->comment = $row["message"];

            $view->profile = sprintf("http://www.facebook.com/profile.php?id=%s",$row["from_id"]) ;
            $params = array(
                "q" => base64_encode(Url::current()) , 
                "comment_id" => $row["comment_id"]);

            $view->hasInvoice = $settings["invoice"];
            $view->invoiceUrl = Url::createUrl("/app/invoice/new.php", $params);

            $html = Template::render($template,$view);
            return $html ;
        }
        
        static function getInvoice($invoiceRow,$commentRow) {

            $html = NULL ;

            
            $template = "/app/fragments/invoice.tmpl" ;
            $view = new \stdClass;
            
            $view->invoiceId = $invoiceRow["id"];
            $view->name = $invoiceRow["name"];
            $view->email = $invoiceRow["email"];
            $view->quantity = $invoiceRow["quantity"];
            $view->price = $invoiceRow["total_price"];
            $view->createdOn = $invoiceRow["created_on"];
            

            $view->picture = $commentRow["picture"] ;
            $view->post_text = $commentRow["post_text"];
            $view->link = $commentRow["link"];
            $view->comment = $commentRow["message"];
            $view->profile = sprintf("http://www.facebook.com/profile.php?id=%s",$commentRow["from_id"]) ;

            $html = Template::render($template,$view);
            return $html ;
        }
    
    }
}

?>
