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

    // response code = 100 print receipt
    // response code != 100 : show error message and ro.php;

    $orderId = Util::tryArrayKey($_POST,"orderId");
    $orderId = empty($orderId) ? 0 : $orderId; 
    settype($orderId, "integer");

    $pageHtml = "" ;

    if(empty($orderId)) {
        // no clue as to what happened!
        $message = "Error: something went wrong. Please try again.";
        echo AppHtml::messageBox($message);
        exit ;
    }

    $code = Util::tryArrayKey($_POST,"responseCode");
    $code = empty($code) ? 1001 : $code; 
    settype($code,"integer");
    
    
    if($code == AppConstants::ZAAKPAY_TX_OK) {
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
        show_error_page($orderId);
    }
    
?>

<!DOCTYPE html>
<html>

    <head>
        <title> Zaakpay PG response</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h3>Payment gateway response</h3>
                    </div>
                    
                    <?php echo $pageHtml ?>
                    
                </div>
            </div>
        </div>
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>
