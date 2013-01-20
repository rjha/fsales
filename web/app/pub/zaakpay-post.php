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

    $data["merchantIdentifier"] = Zaakpay::IDENTIFIER ;
    $data["orderId"] = $orderId ;
    $data["returnUrl"] = Url::base()."/app/ping/zaakpay-tx.php" ;

    $data["buyerEmail"] = $orderRow["email"] ;
    
    $data["buyerFirstName"] = $orderRow["first_name"] ;
    $data["buyerLastName"] = $orderRow["last_name"] ;
    $data["buyerAddress"] = $orderRow["billing_address"] ;
    $data["buyerCity"] = $orderRow["billing_city"] ;
    $data["buyerState"] = $orderRow["billing_state"] ;
    $data["buyerCountry"] = $orderRow["billing_country"] ;
    $data["buyerPincode"] = $orderRow["billing_pincode"] ;
    $data["buyerPhoneNumber"] = $orderRow["phone"] ;

    $data["shipToFirstname"] = $orderRow["shipping_first_name"] ;
    $data["shipToLastname"] = $orderRow["shipping_last_name"] ;
    $data["shipToAddress"] = $orderRow["shipping_address"] ;
    $data["shipToCity"] = $orderRow["shipping_city"] ;
    $data["shipToState"] = $orderRow["shipping_state"] ;
    $data["shipToCountry"] = $orderRow["shipping_country"] ;
    $data["shipToPincode"] = $orderRow["shipping_pincode"] ;
    $data["shipToPhoneNumber"] = $orderRow["shipping_phone"] ;  

    $data["currency"] = $orderRow["currency"] ;
    $data["amount"] = $orderRow["total_price"] * 100 ;
    $data["merchantIpAddress"] = $orderRow["ip_address"] ;
    // DDDD-MM-YY

    $data["txnDate"] = date("Y-m-d",strtotime("now")) ;
    $data["productDescription"] = Util::abbreviate($orderRow["item_description"],70) ;

    // 1 = debit/credit card
    // 3 = net banking
    $data["txnType"] = 1 ;
    // option API = 3 
    $data["zpPayOption"] = 3 ;
    // mode = 0 for dev, 1 for production
    $zaakpay_mode = Config::getInstance()->get_value("fs.zaakpay.mode", "development");
    $data["mode"]  = (strcmp($zaakpay_mode,"production") == 0 ) ? 1 : 0  ;
    // 0=Service, 1=Goods, 2=Auction, 3=Others
    $data["purpose"] = 3 ;

    $checksum = Zaakpay::calculateChecksum($data);
    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Zaakpay payment gateway</title>
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
                            Do Not Refresh or Press Back button in browser <br/> 
                            Redirecting to Zaakpay ...
                            

                        </p>
                         <div class="p20">
                            <img src="/css/asset/fs/fb_loader.gif" alt="ajax loader" />
                        </div>
                    </div>

                    <div class="form-wrapper">
                        <form action="https://api.zaakpay.com/transact" method="post">
                            <?php \com\indigloo\fs\zaakpay\Helper::outputForm($data,$checksum); ?>
                        </form>
                    </div>
                    
                    
                </div>
            </div>
        </div>
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>