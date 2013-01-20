<?php
    header('Content-type: application/json');
    include ('fs-app.inc');
    include(APP_WEB_DIR . '/app/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Constants as Constants ;

    use \com\indigloo\fs\auth\Login as Login;
    use \com\indigloo\fs\mail\Application as AppMail ;
    use \com\indigloo\fs\Constants as AppConstants ;


    set_exception_handler('webgloo_ajax_exception_handler');
    $message = NULL ;
    
    function send_error($code,$message) {
        $response = array(
            "code" => $code , 
            "message" => $message);
        $html = json_encode($response);
        echo $html;
        exit;
    }

    if(!Login::hasSession()) {
        $code = 401 ;
        $message = "Authentication failure : You need to login!" ;
        send_error($code,$message);
    }


    $invoiceId = Util::getArrayKey($_POST, "invoiceId");
    // get invoice data
    
    $invoiceDao = new \com\indigloo\fs\dao\Invoice();   
    $invoiceRow = $invoiceDao->getOnId2($invoiceId);
    $code = AppMail::send_invoice($invoiceRow);
    $message = NULL ;

    if($code > 0 ) {
        // mail error 
        $code = 500 ;
        $message = " Error: sending mail. please try again!";
        send_error($code,$message);
         
    } else {
        $invoiceDao->setOpBit($invoiceId,AppConstants::INVOICE_PENDING_STATE) ; 
        $message = sprintf("invoice # %d sent to buyer",$invoiceId) ;

    }

    $response = array("code" => 200, "message" => $message);
    $html = json_encode($response);
    echo $html;
?>