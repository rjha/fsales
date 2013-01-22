<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');

    use \com\indigloo\Util as Util;
    use \com\indigloo\Constants as Constants ;

    use \com\indigloo\fs\auth\Login as Login;
    use \com\indigloo\fs\Constants as AppConstants ;
    use \com\indigloo\fs\html\Application as AppHtml;


    set_exception_handler('webgloo_ajax_exception_handler');
    $message = NULL ;
    $invoiceId = Util::getArrayKey($_POST, "invoiceId");
    
 
    $invoiceDao = new \com\indigloo\fs\dao\Invoice();
    $orderRow = $invoiceDao->getOrderOnId($invoiceId);

    $html = AppHtml::getShipping($orderRow);
    echo $html;
?>
