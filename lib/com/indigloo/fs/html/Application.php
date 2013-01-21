<?php

namespace com\indigloo\fs\html {

    use \com\indigloo\Template ;
    use \com\indigloo\Url ;
    use \com\indigloo\Util ;

    use \com\indigloo\fs\Constants as AppConstants;
    use \com\indigloo\fs\zaakpay\Helper as ZaakpayHelper ;
    
    class Application {

        static function getInvoiceState($state) {
            // we need to return 
            // 1. status text
            // 2. action link + name pairs 
            // explanation of states
            // 1- newly created 
            // 2 - pending : email sent - user response awaited
            // 3- user created order out of invoice by providing data
            // 4- check with PG - payment went through
            // 5- item shipped
            // 6 - closed
            
            $status = array(1 => "New", 
                2 => "Pending", 
                3 => "Processing" , 
                4 => "Paid",
                5 => "Shipped");
            $text = isset($status[$state]) ? $status[$state] : "Unknown" ;

            $editLink = array("name" => '<i class="icon icon-edit"></i> edit' , "rel" => "edit");
            $mailLink = array("name" => '<i class="icon icon-envelope"></i> send invoice' , "rel" => "mail");
            $cancelLink = array("name" => '<i class="icon icon-remove"></i> cancel', "rel" => "cancel");
            
            $shippingLink = array("name" => '<i class="icon icon-gift"></i> shipping' , "rel" => "shipping");
            $reminderLink = array("name" => '<i class="icon icon-bell"></i> remind', "rel" => "reminder");

            $links = array( 
                1 => array($editLink,$mailLink),
                2 => array($mailLink),
                3 => array($mailLink),
                4 => array($shippingLink,$mailLink),
                5 => array($reminderLink));

            $actions = isset($links[$state]) ? $links[$state] : array();
            $data = array("text" => $text, "actions" => $actions);
            return $data ;
            
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
        
        static function getTxReceipt($orderId,$code,$response) {

            $html = NULL ;
            $template = "/app/fragments/tx-receipt.tmpl" ;
            $view = new \stdClass;

            $view->orderId = $orderId ;
            $view->code = $code ;
            $view->message = $response ;

            $html = Template::render($template,$view);
            return $html ;

        }


        /*
         * @todo figure out the checksum calculation first
        static function getZaakpayForm($data,$checksum) {

            $html = NULL ;
            foreach($data as $key => $value) {

                $data[$key] = ($key == 'returnUrl') ? 
                    ZaakpayHelper::sanitizedURL($value) : ZaakpayHelper::sanitizedParam($value) ;
                
                $template = "/app/fragments/zaakpay-form.tmpl" ;
                $view = new \stdClass;
                $view->data = $data ;
                $view->checksum = $checksum;
                $html = Template::render($template,$view);
                return $html ;

            }

        } */

        /**
         * @param qparams request query parameters
         *
         */
        static function getSource($selectedRow, $allRows,$qparams) {
            $html = NULL ;
            $num_pages = sizeof($allRows);
            $template = ($num_pages == 1) ? "/app/fragments/source2.tmpl" : "/app/fragments/source.tmpl" ;

            $view = new \stdClass;
            $commentData = array();

            //copy request parameters for comments
            $cparams = $qparams ;
            // destroy ft
            unset($cparams["ft"]);
            // base URL for comment
            $baseURI = Url::createUrl(AppConstants::DASHBOARD_URL,$cparams) ;

            // first filter for comments
            $c1link = Url::addQueryParameters($baseURI, array("ft" => AppConstants::ALL_COMMENT_FILTER));
            $c1 = array("name" => "show all comments", "link" =>$c1link);
            array_push($commentData,$c1);

            $c2link = Url::addQueryParameters($baseURI, array("ft" => AppConstants::VERB_COMMENT_FILTER));
            $c2 = array("name" => "only show comments with buyit", "link" =>$c2link);
            array_push($commentData,$c2);

            $sourceData = array() ;

            if($num_pages > 1 ) {
                //source rows
                unset($qparams["source_id"]);

                foreach($allRows as $srow) {
                    $link = Url::createUrl(AppConstants::DASHBOARD_URL, $qparams);
                    $link = Url::addQueryParameters($link, array("source_id" => $srow["source_id"]));
                    $sdata = array("name" => $srow["name"], "link" => $link);
                    array_push($sourceData,$sdata);
                }
            }

            $view->sourceData = $sourceData ;
            $view->commentData = $commentData ;
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

        static function getNoComment($sourceRow) {
            $html = NULL ;
            $template = "/app/fragments/no-comment.tmpl" ;
            $view = new \stdClass;
            $view->lastTime = date("d-M h:i A",$sourceRow["last_stream_ts"]);
            
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

            $view->profile = sprintf(AppConstants::FACEBOOK_PROFILE_URL,$row["from_id"]) ;
            $params = array(
                "q" => base64_encode(Url::current()) , 
                "comment_id" => $row["comment_id"]);

            $view->showInvoiceButton = $settings["invoice"];
            $view->invoiceUrl = Url::createUrl(AppConstants::NEW_INVOICE_URL, $params);

            $view->indicator = '' ;

            if($row["has_invoice"] > 0 ) {
                $view->indicator = '<span> <i class="icon icon-star"> </i></span>' ;
            }

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

            $stateData = self::getInvoiceState($invoiceRow["op_bit"]);
            $view->status = $stateData["text"];
            $view->actions = $stateData["actions"];

            $view->picture = $invoiceRow["picture"] ;
            $view->post_text = $invoiceRow["post_text"];
            $view->link = $invoiceRow["link"];
            $view->profile = sprintf(AppConstants::FACEBOOK_PROFILE_URL,$invoiceRow["from_id"]) ;

            $html = Template::render($template,$view);
            return $html ;
        }

         static function getInvoicePreview($state,$invoiceId) {
            if($state > 1 ) { return "" ;}

            $html = NULL ;

            $template = "/app/fragments/invoice-preview.tmpl" ;
            $view = new \stdClass;
            
            $qUrl = Url::createUrl("/app/invoice/preview.php", array("invoice_id" => $invoiceId));

            $params = array("invoice_id" => $invoiceId,"q" => base64_encode($qUrl));
            $view->editUrl = Url::createUrl(AppConstants::EDIT_INVOICE_URL,$params);
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
            
            $checkout_link = AppConstants::WWW_CHECKOUT_URL ;
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
