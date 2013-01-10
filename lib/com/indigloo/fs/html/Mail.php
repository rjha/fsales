<?php

namespace com\indigloo\fs\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;
    
    class Mail {

           
        static function getInvoice($invoiceRow,$commentRow) {

            $html = NULL ;

            
            $template = "/app/fragments/mail/invoice.tmpl" ;
            $view = new \stdClass;
            
            $view->invoiceId = $invoiceRow["id"];
            $view->name = $invoiceRow["name"];
            $view->email = $invoiceRow["email"];
            $view->quantity = $invoiceRow["quantity"];
            $view->price = $invoiceRow["total_price"];
            $view->createdOn = $invoiceRow["created_on"];
            $view->sourceName = $invoiceRow["source_name"] ;           

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
