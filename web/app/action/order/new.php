<?php
    
    include 'fs-app.inc';
    include(APP_WEB_DIR . '/app/inc/header.inc');
    // This page is open to all
    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger ;
    

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\Constants as AppConstants;


    $gWeb = \com\indigloo\core\Web::getInstance(); 
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler('form1', $_POST);

        
        // Rules
        // Rule number zero : never trust user input!
        // first name/ last name - min :3 max 30 | alphanumeric
        // first name <> last name
        // email : 64/ valid format
        // phone : digits only / max 16
        // address : 100 / min 6 
        // city : 3-30 chars
        // state : required
        // pincode 2-12 : numbers only
        

        $fhandler->addRule('invoice_id', 'INVOICE ID', array('required' => 1));
        $fhandler->addRule('first_name', 'First name', array('required' => 1, 'minlength' =>3, 'maxlength' => 30));
        $fhandler->addRule('last_name', 'Last name', array('required' => 1,'minlength' =>3, 'maxlength' => 30));
        
        $fhandler->addRule('email', 'Email', array('required' => 1));
        $fhandler->addRule('phone', 'Phone', array('required' => 1, 'maxlength' => 16));

        $fhandler->addRule('billing_address', 'Address (billing)', array('required' => 1, 'minlength' =>6, 'maxlength' => 100));
        $fhandler->addRule('billing_city', 'City (billing)', array('required' => 1,'minlength' =>3, 'maxlength' => 30));
        $fhandler->addRule('billing_state', 'State (billing)', array('required' => 1));
        $fhandler->addRule('billing_pincode', 'Pincode (billing)', array('required' => 1,'minlength' =>2, 'maxlength' => 12));


        $fhandler->addRule('checksum', 'checksum', array('required' => 1, 'rawData' => 1));

        $fhandler->addRule('ship_first_name', 'First name (shipping)', array('required' => 1, 'minlength' =>3, 'maxlength' => 30));
        $fhandler->addRule('ship_last_name', 'Last name (shipping)', array('required' => 1,'minlength' =>3, 'maxlength' => 30));
        $fhandler->addRule('ship_phone', 'Phone (shipping)', array('required' => 1, 'maxlength' => 16));

        $fhandler->addRule('ship_address', 'Address (shipping)', array('required' => 1, 'minlength' =>6, 'maxlength' => 100));
        $fhandler->addRule('ship_city', 'City (shipping)', array('required' => 1,'minlength' =>3, 'maxlength' => 30));
        $fhandler->addRule('ship_state', 'State (shipping)', array('required' => 1));
        $fhandler->addRule('ship_pincode', 'Pincode (shipping)', array('required' => 1,'minlength' =>2, 'maxlength' => 12));


        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        $invoiceDao = new \com\indigloo\fs\dao\Invoice();
        $orderDao = new \com\indigloo\fs\dao\Order();
        $orderDao->validateForm($fvalues);

        $invoiceId = $fvalues["invoice_id"];
        settype($invoiceId, "integer");

        // valid checkout_token and invoice_id ?
        $checkout_token  =  $gWeb->find("fs:checkout:token",true);
        $formString =  sprintf("%d:%s",$invoiceId,$checkout_token ) ;
        $checksum = hash_hmac("sha256", $formString , AppConstants::SECRET_KEY);

        // calculated checksum = form.checksum ?

        if(strcmp($checksum,$fvalues["checksum"]) != 0 ) {    
            $message = "Error: form data has been changed : checksum do not match!" ;
            throw new UIException(array($message));
        }

        $invoiceRow = $invoiceDao->getOnId2($invoiceId);
        if(empty($invoiceRow)) {
            $message = "Error: No invoice found!" ;
            throw new UIException(array($message));
        }
        
        $orderId = $orderDao->add($invoiceRow,$fvalues);

        // get order id and show redirect-to-mobikwik screen
        // make sure you have the right data before fwd-ing to 
        // mobikwik
        $params = array("order_id" => $orderId);
        $fwd = Url::createUrl("/app/pub/mobikwik-post.php", $params);
        header("Location: ".$fwd);

    } catch(UIException $ex) {
        
        $gWeb->store(Constants::STICKY_MAP, $fvalues);
        $gWeb->store(Constants::FORM_ERRORS,$ex->getMessages());

        // decode furl  for use
        $fwd = base64_decode($fUrl);
        header("Location: " . $fwd);
        exit(1);

    }catch(\Exception $ex) {
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
