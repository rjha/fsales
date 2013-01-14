<?php
    
    include 'fs-app.inc';
    include(APP_WEB_DIR . '/app/inc/header.inc');
    // This page is open to all
    
    use \com\indigloo\ui\form as Form;
    use \com\indigloo\Constants as Constants ;
    use \com\indigloo\Util as Util ;

    use \com\indigloo\Url as Url ;
    use \com\indigloo\exception\UIException as UIException;
    use \com\indigloo\fs\auth\Login as Login ;

    $gWeb = \com\indigloo\core\Web::getInstance(); 
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler('form1', $_POST);

        // @todo : add min and max rules
        // @todo add email rule

        $fhandler->addRule('invoice_id', 'INVOICE ID', array('required' => 1));
        $fhandler->addRule('first_name', 'First Name', array('required' => 1));
        $fhandler->addRule('last_name', 'Last Name', array('required' => 1));
        $fhandler->addRule('email', 'Email', array('required' => 1));

        $fhandler->addRule('total_price', 'Amount', array('required' => 1));
        $fhandler->addRule('phone', 'Phone', array('required' => 1));

        $fhandler->addRule('billing_address', 'Billing Address', array('required' => 1));
        $fhandler->addRule('billing_city', 'City (Billing)', array('required' => 1));
        $fhandler->addRule('billing_state', 'State (Billing)', array('required' => 1));
        $fhandler->addRule('billing_pincode', 'Pincode (Billing)', array('required' => 1));

        // @todo : add shipping stuff
        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        // push this data into fs_orders
        
        $invoiceId = $fvalues["invoice_id"];
        settype($invoiceId, "integer");
        $invoiceDao = new \com\indigloo\fs\dao\Invoice(); 
        //@todo : write this method
        $invoiceRow = $invoiceDao->getOrderDataOnId($invoiceId);
    
        if(empty($invoiceRow)) {
            //@todo throw error
        }

        //@todo : add shipping stuff as well
        $orderDao = new \com\indigloo\fs\dao\Order();
        $orderId = $orderDao->add($invoiceRow,$fvalues);

        // get order id and show redirect-to-zaakpay screen
        
        $params = array("order_id" => $orderId);
        $fwd = Url::createUrl("/app/zaakpay/tx-post.php", $params);
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
