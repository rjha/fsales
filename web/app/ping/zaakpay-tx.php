<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use \com\indigloo\Util ;
    use \com\indigloo\Url ;

    use \com\indigloo\fs\html\Application as AppHtml ;
    use \com\indigloo\fs\Constants as AppConstants ;

    // response code = 100 print receipt
    // response code != 100 : show error message and ro.php;

    $orderId = Util::tryArrayKey($_POST,"orderId");
    $orderId = empty($orderId) ? 0 : $orderId; 
    settype($orderId, "integer");

    if(empty($orderId)) {
        // no clue as to what happened!
        $message = "Error: something went wrong. Please try again.";
        echo AppHtml::messageBox($message);
        exit ;
    }

    $code = Util::tryArrayKey($_POST,"responseCode");
    $code = empty($code) ? 1001 : $code; 

    $response = Util::tryArrayKey($_POST,"responseDescription");
    if(empty($response)) {
        $response = "Error: something went wrong. Please try again.";
    }

    $pageHtml = AppHtml::getTxReceipt($orderId,$code,$response);
    // @todo response code = 100 => update fs_order state
    // response code 100 orders cannot be tried again.
    

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
