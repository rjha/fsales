<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use \com\indigloo\Util ;
    use \com\indigloo\Url ;
    use \com\indigloo\Constants ;
    use \com\indigloo\Logger ;

    use \com\indigloo\fs\html\Application as AppHtml ;
    use \com\indigloo\fs\Constants as AppConstants ;

    function show_error_page($orderId) {
        
        $response = " Error: There was a problem processing your transaction.";
        $params = array("order_id" => $orderId);
        $fwd = Url::createUrl("/app/pub/ro.php",$params) ;
        $gWeb = \com\indigloo\core\Web::getInstance();

        $errors = array($response);
        array_push($errors," Please check the information below and try again.");

        $gWeb->store(Constants::FORM_ERRORS, $errors);
        header("Location: ".$fwd);

    }

    // for mobikwik
    // statuscode = 0  is success - print receipt
    // statuscode != 0 : show error message and ro.php;

    $orderId = Util::tryArrayKey($_REQUEST,"orderid");
    $orderId = empty($orderId) ? 0 : $orderId; 
    settype($orderId, "integer");

    $pageHtml = "" ;

    if(empty($orderId)) {
        // no clue as to what happened!
        
        $message = "Error: something went wrong. Please try again.";
        echo AppHtml::messageBox($message);
        exit ;
    }

    $code = Util::tryArrayKey($_REQUEST,"statuscode");
    // PHP pitfalls
    // 1) settype($code, "integer") will also set 
    // non numeric garbage values like $#@ to 0
    // 2) $code == 0 will return TRUE for code = $# also
    // 3) $code == 0 : empty($code) will return TRUE

    // code == 0 will fall in empty check 
    // is_null check is needed for code zero.
    $code = Util::tryEmpty($code) ? 1001 : $code; 

    if(ctype_digit($code) && ($code == 0)) {
        try{
            
            $orderDao = new \com\indigloo\fs\dao\Order();
            $orderDao->setState($orderId,AppConstants::ORDER_TX_OK_STATE);
            $response = "Your transaction was successful.";
            $pageHtml = AppHtml::getTxReceipt($orderId,$code,$response);

        } catch(\Exception $ex) {
            Logger::getInstance()->error($ex->getMessage());
            Logger::getInstance()->backtrace($ex->getTrace());
            show_error_page($orderId);
        }

    }else {
        // log message from mobikwik
        $tx_message = Util::tryArrayKey($_REQUEST, "statusmessage"); 
        $tx_message = Util::tryEmpty($tx_message) ? "No message from mobikwik" : $tx_message ;
        Logger::getInstance()->error($tx_message);

        show_error_page($orderId);
    }
    
?>

<!DOCTYPE html>
<html>

    <head>
        <title> Mobikwik wallet response</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h3>Mobikwik wallet response</h3>
                    </div>
                    
                    <?php echo $pageHtml ?>
                    
                </div>
            </div>
        </div>
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>
