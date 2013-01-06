<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');

    use com\indigloo\Constants as Constants;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\fs\auth\Login as Login ;

    try{

        $gWeb = \com\indigloo\core\Web::getInstance();
        // destroy the session variable
        $pages = $gWeb->find("fs.user.pages",true);
        $fwd = "/app/dashboard.php" ;
        header("location: ".$fwd);
        exit(1);
       
        
    }catch(\Exception $ex) {
        
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $message = "Error: something went wrong!" ;
        $gWeb->store("fs.router.message",$message);
        $fwd = "/app/router.php?q=". base64_encode("/app/dashboard.php");
        header("location: ".$fwd);
        exit(1);
    }
    
?>