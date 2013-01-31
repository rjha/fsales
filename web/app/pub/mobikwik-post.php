<?php

    // open to all zaakpay tx-post form
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use \com\indigloo\Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Logger ;
    use \com\indigloo\Configuration as Config;

    use \com\indigloo\fs\html\Application as AppHtml;
    use \com\indigloo\fs\zaakpay\Helper as Zaakpay ;
    
    $orderId = Url::tryQueryParam("order_id");
    if(empty($orderId)) {
        $message = "Error: No order_id found in request!";
        $html = AppHtml::messageBox($message);
        echo $html ;
        exit ;
    }

    settype($orderId, "integer");

    $orderDao = new \com\indigloo\fs\dao\Order();  
    $orderRow = $orderDao->getOnId($orderId);

    if(empty($orderRow)) {
        $message = "Error: No order found for this request!";
        $html = AppHtml::messageBox($message);
        echo $html ;
        exit ;
    }
    
    //prepare data for form
    $data = array();
    $data["email"] = $orderRow["email"] ;
    $data["cell"] = $orderRow["phone"] ;
    $data["amount"] = ceil($orderRow["total_price"]) ;
    $data["redirecturl"] = Url::base()."/app/ping/mobikwik-tx.php" ;
    $data["mid"] = Zaakpay::MOBIKWIK_MID ;
    $data["merchantname"] = "Favsales.com" ;
    $data["orderid"] = $orderId ;
    
    $checksum = Zaakpay::calculateChecksum($data,Zaakpay::MOBIKWIK_SECRET_KEY);
    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Mobikiwk wallet redirect </title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>
        <script type="text/javascript">
            
            function submitForm(){
                var form = document.forms[0];
                form.submit();
            }
            
        </script>
</head>

    </head>

     <body onload="javascript:submitForm()">
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h2>&nbsp;</h2>
                    </div>
                   
                    <div class="mt20">
                        <p class="lead"> 
                            Redirecting to Mobikwik wallet ... <br>
                            Do Not Refresh or Press Back button in browser <br/>
                        </p>
                         <div class="p20">
                            <img src="/css/asset/fs/fb_loader.gif" alt="ajax loader" />
                        </div>
                    </div>

                    <div class="form-wrapper">
                        <form action="http://www.mobikwik.com/views/proceedtowalletpayment.jsp" method="post">
                            <?php \com\indigloo\fs\zaakpay\Helper::outputForm($data,$checksum); ?>
                        </form>
                    </div>
                    
                    
                </div>
            </div>
        </div>
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>