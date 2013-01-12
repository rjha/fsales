<?php
    
    include 'fs-app.inc';
    include(APP_WEB_DIR . '/app/inc/header.inc');
    include(APP_WEB_DIR . '/app/inc/role/user.inc');

    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;

    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\mail\Application as AppMail ;


    $gWeb = \com\indigloo\core\Web::getInstance(); 
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler('form1', $_POST);
        $fhandler->addRule('invoice_id', 'Invoice_ID', array('required' => 1));
        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        $invoiceId = $fvalues["invoice_id"];

        // get invoice data
        $invoiceDao = new \com\indigloo\fs\dao\Invoice();
        $commentDao = new \com\indigloo\fs\dao\Comment();

        $invoiceRow = $invoiceDao->getOnId($invoiceId);
        $commentId = $invoiceRow["comment_id"];
        $commentRow = $commentDao->getOnId($commentId);

        $code = AppMail::send_invoice($invoiceRow,$commentRow);
        // @todo change status/ show error based on code
        //$invoiceDao->changeStatus($invoiceId,2) ;

        //success - go to invoice show screen
        $params = array("invoice_id" => $invoiceId);
        $fwd = Url::createUrl("/app/invoice/show.php", $params);
        header("Location: ".$fwd);

    } catch(UIException $ex) {
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());

        // decode furl  for use
        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        exit(1);
    }

    
?>
