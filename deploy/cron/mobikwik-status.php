#!/usr/bin/php
<?php
	
	include("fs-app.inc");
	include(APP_CLASS_LOADER);
	include(APP_WEB_DIR."/app/inc/global-error.inc");

	use \com\indigloo\fs\zaakpay\Helper as Zaakpay ;

	set_exception_handler('offline_exception_handler');

	
    $data = array(
            "action" => "gettxnstatus",
            "mid" => Zaakpay::MOBIKWIK_MID ,
            "orderId" => "19");

    
    $checksum = Zaakpay::calculateChecksum($data,Zaakpay::MOBIKWIK_SECRET_KEY);
    $data["checksum"] = $checksum;

    $postdata = http_build_query($data);
    $length = strlen($postdata);

    $headers = array(
    	"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
        "Content-Length: ".$length);

    $url = "http://www.mobikwik.com/wallet.do" ;
	$ch = curl_init();
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:10.0.7) Gecko/20100101 Firefox/10.0.7 Iceweasel/10.0.7");
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);

    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt ($ch, CURLOPT_POST, 1);

    $result = curl_exec ($ch);
    echo $result ;
        

?>
