<?php

namespace com\indigloo\fs\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;
    
    class Application {

        static function getInvoiceState($state) {
            $text = "Unknown" ;

            switch($state) {
                case 1 : 
                    $text = "New" ;
                    break ;
                case 2 :
                    $text = "Mail sent" ;
                    break ;
                case 3 :
                    $text = "Paid" ;
                    break ;
                case 4 :
                    $text = "Shipped" ;
                    break ;
                case 5 :
                    $text = "Closed" ;
                    break ;
                default :
                    $text = "Unknown" ;

            }

            return $text ;
        }

        static function getInvoiceActions($invoiceId,$state) {
            //Actions are array of action - link
            $actions = array() ;

        }

        static function messageBox($message) {
            $message = empty($message) ? "No message supplied!" : $message;
             
            $html = NULL ;
            $template = "/app/fragments/message-box.tmpl" ;
            $view = new \stdClass;
            $view->message = $message ;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getPageTable($pages,$sources) {
            if(empty($pages) && empty($sources)) { return ""; }

            // source in page - should be checked + highlighted
            // source not in page - should be unchecked + highlighted

            $html = NULL ;
            $template = "/app/fragments/page/table.tmpl" ;

            $rows = array();
            $sourceIds = array();
            $pageIds = array();
            
            foreach($sources as $source) {
                array_push($sourceIds, $source["source_id"]);
            }

            foreach($pages as $page) {
                array_push($pageIds,$page["id"]);
                //needle,haystack
                // page["id"] is stored as fs_source.source_id
                if(!in_array($page["id"],$sourceIds)) {
                    //fresh pages
                    $page["rowClass"] = "" ;
                    $page["checked"] = "" ;
                    array_push($rows,$page);

                }
                
            }

            foreach($sources as $source) {
                $row = array();
                $row["id"] = $source["source_id"];
                $row["name"] = $source["name"];

                if(in_array($source["source_id"],$pageIds)) {
                    $row["rowClass"] = "success" ;
                    $row["checked"] = "checked" ;
                }else {
                    $row["rowClass"] = "warning" ;
                    $row["checked"] = "" ;
                }

                array_push($rows,$row);
                 
            }

            $view = new \stdClass;
            $view->rows = $rows ;
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
            $view->invoiceId = $row["has_invoice"];

            $view->profile = sprintf("http://www.facebook.com/profile.php?id=%s",$row["from_id"]) ;
            $params = array(
                "q" => base64_encode(Url::current()) , 
                "comment_id" => $row["comment_id"]);

            $view->hasInvoice = $settings["invoice"];
            $view->invoiceUrl = Url::createUrl("/app/invoice/new.php", $params);

            $html = Template::render($template,$view);
            return $html ;
        }

        static function getNoInvoice() {
            $html = NULL ;
            $template = "/app/fragments/no-invoice.tmpl" ;
            $view = new \stdClass;
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getInvoice($invoiceRow) {

            $html = NULL ;

            $template = "/app/fragments/invoice.tmpl" ;
            $view = new \stdClass;
            
            $view->invoiceId = $invoiceRow["id"];
            $view->name = $invoiceRow["name"];
            $view->email = $invoiceRow["email"];
            $view->quantity = $invoiceRow["quantity"];

            $view->totalPrice = $invoiceRow["total_price"];
            $view->unitPrice = $invoiceRow["unit_price"];
            $view->createdOn = $invoiceRow["created_on"];
            $view->status = self::getInvoiceState($invoiceRow["op_bit"]);

            $view->picture = $invoiceRow["picture"] ;
            $view->post_text = $invoiceRow["post_text"];
            $view->link = $invoiceRow["link"];
            $view->profile = sprintf("http://www.facebook.com/profile.php?id=%s",$invoiceRow["from_id"]) ;

            $html = Template::render($template,$view);
            return $html ;
        }

         static function getInvoicePreview($state,$invoiceId) {
            if($state > 1 ) { return "" ;}

            $html = NULL ;

            $template = "/app/fragments/invoice-preview.tmpl" ;
            $view = new \stdClass;
            
            $params = array("invoice_id" => $invoiceId);
            $view->editUrl = Url::createUrl("/app/invoice/edit.php",$params);
            $view->invoiceId = $invoiceId;
            
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getCheckoutInvoice($invoiceRow) {

            $html = NULL ;

            $template = "/app/fragments/checkout-invoice.tmpl" ;
            $view = new \stdClass;
            
            $view->invoiceId = $invoiceRow["id"];
            $view->name = $invoiceRow["name"];
            $view->quantity = $invoiceRow["quantity"];
            $view->price = $invoiceRow["total_price"];

            $view->picture = $invoiceRow["picture"] ;
            $view->post_text = $invoiceRow["post_text"];
            $view->link = $invoiceRow["link"];

            $view->createdOn = $invoiceRow["created_on"];
            $view->sourceName = $invoiceRow["source_name"];
            
            $html = Template::render($template,$view);
            return $html ;
        }

        static function getMailInvoice($invoiceRow) {

            $html_tmpl = "/app/fragments/mail/html/invoice.tmpl" ;
            $text_tmpl = "/app/fragments/mail/text/invoice.tmpl" ;

            $view = new \stdClass;
            
            $view->invoiceId = $invoiceRow["id"];
            $view->userName = $invoiceRow["name"];
            $view->price = $invoiceRow["total_price"];
            $view->sourceName = $invoiceRow["source_name"] ;           
            

            $view->post_text = $invoiceRow["post_text"];
            $view->post_link = $invoiceRow["link"];
            
            $crypt = Util::encrypt($view->invoiceId);
            $checkout_link = Url::base()."/app/pub/checkout.php";
            $params = array("invoice_id" => urlencode($view->invoiceId));
            $view->checkout_link = Url::createUrl($checkout_link,$params);

            $html = Template::render($html_tmpl,$view);
            $text = Template::render($text_tmpl,$view);
            $mail_body = array("html" => $html, "text" => $text );
            return $mail_body ;
        }

    
    }
}

?>
