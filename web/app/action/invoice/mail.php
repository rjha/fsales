<?php
    
    include 'fs-app.inc';
    include(APP_WEB_DIR . '/app/inc/header.inc');
    include(APP_WEB_DIR . '/app/inc/role/user.inc');

    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;

    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\mail\Application as AppMail ;
    use \com\indigloo\fs\Constants as AppConstants ;


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
        $invoiceRow = $invoiceDao->getOnId2($invoiceId);
        $code = AppMail::send_invoice($invoiceRow);

        if($code > 0 ) {
            // mail error 
            $message = " Error: sending mail. please try again!";
            throw new UIException(array($message));
        } else {
            $invoiceDao->setOpBit($invoiceId,AppConstants::INVOICE_MAIL_SENT_BIT) ;
        }

        $message = " invoice mail sent!";
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_MESSAGES, array($message));

        // decode fUrl  for use
        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        exit(1);

    }catch(UIException $ex) {
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());

        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        exit(1);

    } catch(\Exception $ex) {
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $message = " Error: something went wrong!" ;
        $gWeb->store(Constants::FORM_ERRORS,array($message));

        // decode fUrl  for use
        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        exit(1);
    }
    
?>
