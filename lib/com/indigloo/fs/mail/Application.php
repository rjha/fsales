<?php

namespace com\indigloo\fs\mail {
   
    use \com\indigloo\Util ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\mail\SendGrid as WebMail ;

    use \com\indigloo\fs\html\Application as AppHtml;
    
    class Application {

        static function send_invoice($invoiceRow) {
            
           
            //@todo - store this mail? fwd to another address?
            $tos = array($invoiceRow["email"]);
            $from = Config::getInstance()->get_value("default.mail.address");
            $fromName = Config::getInstance()->get_value("default.mail.name");
            
            $subject = sprintf(" Your invoice from %s",$invoiceRow["source_name"]) ;
            $mail_body = AppHtml::getMailInvoice($invoiceRow);
            $html = $mail_body["html"];
            $text = $mail_body["text"];
            
            $code =  WebMail::sendViaWeb($tos,$from,$fromName,$subject,$text,$html);
            return $code ;
            
        }

    }
}

?>
