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

    $gWeb = \com\indigloo\core\Web::getInstance(); 
    $fvalues = array();
    $fUrl = \com\indigloo\Url::tryFormUrl("fUrl");

    try{
        
        $fhandler = new Form\Handler('form1', $_POST);
        $fhandler->addRule('name', 'Name', array('required' => 1));
        $fhandler->addRule('email', 'Email', array('required' => 1));
        $fhandler->addRule('unit_price', 'Unit Price', array('required' => 1));
        $fhandler->addRule('quantity', 'Quantity', array('required' => 1));
        $fhandler->addRule('comment_id', 'Comment_ID', array('required' => 1));

        // get form values
        $fvalues = $fhandler->getValues();
        
        if ($fhandler->hasErrors()) {
            throw new UIException($fhandler->getErrors());
        }

        $invoiceDao = new \com\indigloo\fs\dao\Invoice();
        $loginId = Login::getLoginIdInSession();

        // @todo : check price and quantity for absurd values
        // zero price/ negative quantity etc.
        
        $invoiceId = $invoiceDao->create($loginId,
                                    $fvalues["comment_id"],
                                    $fvalues["name"],
                                    $fvalues["email"],
                                    $fvalues["unit_price"],
                                    $fvalues["quantity"],
                                    $fvalues["seller_info"]);

        
        //success - go to invoice send screen
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
