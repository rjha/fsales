<?php
    header('Content-type: application/json');
    include ('fs-app.inc');
    include(APP_WEB_DIR . '/app/inc/header.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\fs\auth\Login as Login;

    set_exception_handler('webgloo_ajax_exception_handler');
    $message = NULL ;
    sleep(5);
    
    if(!Login::hasSession()) {
         
        $response = array(
            "code" => 401 , 
            "message" => "Authentication failure: You need to login!");
        $html = json_encode($response);
        echo $html;
        exit;
    }

    
    $params = new \stdClass;
    $params->action = Util::tryArrayKey($_POST, "action");
    $params->invoiceId = Util::getArrayKey($_POST, "invoiceId");

    $response = array("code" => 200, "message" => json_encode($params));
    $html = json_encode($response);
    echo $html;
?>
