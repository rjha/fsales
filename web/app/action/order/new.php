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

    $gWeb = \com\indigloo\core\Web::getInstance(); 
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler('form1', $_POST);

        
        // Rules
        // first name/ last name - min :3 max 30 | alphanumeric
        // first name <> last name
        // email : 64/ valid format
        // phone : digits only / max 16
        // address : 100 / min 6 
        // city : 3-30 chars
        // state : required
        // pincode 2-12 : numbers only
        

        $fhandler->addRule('invoice_id', 'INVOICE ID', array('required' => 1));
        $fhandler->addRule('first_name', 'First Name', array('required' => 1, 'minlength' =>3, 'maxlength' => 30));
        $fhandler->addRule('last_name', 'Last Name', array('required' => 1,'minlength' =>3, 'maxlength' => 30));
        
        $fhandler->addRule('email', 'Email', array('required' => 1));
        $fhandler->addRule('phone', 'Phone', array('required' => 1, 'maxlength' => 16));

        $fhandler->addRule('billing_address', 'Address (Billing)', array('required' => 1, 'minlength' =>6, 'maxlength' => 100));
        $fhandler->addRule('billing_city', 'City (Billing)', array('required' => 1,'minlength' =>3, 'maxlength' => 30));
        $fhandler->addRule('billing_state', 'State (Billing)', array('required' => 1));
        $fhandler->addRule('billing_pincode', 'Pincode (Billing)', array('required' => 1,'minlength' =>2, 'maxlength' => 12));

        // @todo : add shipping stuff
        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        // email check
        if(!Util::contains($fvalues["email"], '@')) {
            $message = "email is not in valid format" ;
            throw new UIException(array($message));
        }

        // phone check
        if(!ctype_digit($fvalues["phone"])) {
            $message = "only numbers are allowed in a phone number" ;
            throw new UIException(array($message));
        }

        // pincode check
        if(!ctype_digit($fvalues["billing_pincode"])) {
            $message = "only numbers are allowed in a Pincode(billing)" ;
            throw new UIException(array($message));
        }

        //first name /last name can be alphanumeric only
        if(!ctype_alnum($fvalues["first_name"]) || !ctype_alnum($fvalues["last_name"])) {
            $message = "Name can be composed of letters and numbers only." ;
            throw new UIException(array($message));
        }

        // first name <> last name check
        if(strcmp($fvalues["first_name"],$fvalues["last_name"]) == 0) {
            $message = "first name and last name cannot be same." ;
            throw new UIException(array($message));
        }

        // push this data into fs_orders
        
        $invoiceId = $fvalues["invoice_id"];
        settype($invoiceId, "integer");
        $invoiceDao = new \com\indigloo\fs\dao\Invoice(); 
        $invoiceRow = $invoiceDao->getOnId2($invoiceId);
    
        if(empty($invoiceRow)) {
            $message = "Error: No invoice found!" ;
            throw new UIException(array($message));
        }

        //@todo : add shipping stuff as well
        $orderDao = new \com\indigloo\fs\dao\Order();
        $orderId = $orderDao->add($invoiceRow,$fvalues);

        // get order id and show redirect-to-zaakpay screen
        // make sure you have the right data before fwd-ing to 
        // zaakpay
        $params = array("order_id" => $orderId);
        $fwd = Url::createUrl("/app/pub/zaakpay-post.php", $params);
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
