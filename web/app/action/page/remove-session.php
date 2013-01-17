<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');

    use \com\indigloo\Url ;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\Logger as Logger;

    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\Constants as AppConstants ;

    try{

        $gWeb = \com\indigloo\core\Web::getInstance();
        // destroy the session variable
        $pages = $gWeb->find("fs.user.pages",true);
        $fwd = AppConstants::DASHBOARD_URL ;
        header("location: ".$fwd);
        exit(1);
       
        
    }catch(\Exception $ex) {
        
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $message = "Error: something went wrong!" ;
        $gWeb->store("fs.router.message",$message);

        $params = array("q" => base64_encode(AppConstants::DASHBOARD_URL));
        $fwd = Url::createUrl(AppConstants::ROUTER_URL,$params); 
        header("location: ".$fwd);
        exit(1);
    }
    
?>