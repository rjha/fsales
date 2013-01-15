<?php

	// open to all zaakpay tx-post form
	include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use \com\indigloo\Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\fs\html\Application as AppHtml;
  	
    $orderId = Url::tryQueryParam("order_id");
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
	 

?>
