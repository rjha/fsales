<?php

namespace com\indigloo\fs\mail {
   
    use \com\indigloo\Util ;
    use \com\indigloo\mail\SendGrid as WebMail ;

    class Application {

        static function send_invoice($invoiceRow,$commentRow) {
            
            $html = AppHtml::getInvoiceMail($invoiceRow,$commentRow);

            $tos = array($invoiceRow["email"]);
            $from = "app@favsales.com" ;
            $fromName = "Favsales app";
            $subject = " Invoice for your purchase at ".$invoiceRow["source_name"] ;
            $text = $html ;
            
            $code =  WebMail::sendViaWeb($tos,$from,$fromName,$subject,$text,$html);
            return $code ;
            
        }

    }
}

?>
