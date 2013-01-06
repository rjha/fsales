<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');

    use com\indigloo\Constants as Constants;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\fs\auth\Login as Login ;

    try{

        $gWeb = \com\indigloo\core\Web::getInstance();
        $pages = $gWeb->find("fs.user.pages",true);
        $loginId = Login::getLoginIdInSession();
        
        // store pages in DB
        $pageDao = new \com\indigloo\fs\dao\Page();
        $pageDao->create($loginId,$pages);
        
    }catch(\Exception $ex) {
        
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $message = "Error: something went wrong!" ;
        $gWeb->store("fs.router.message",$message);
        $fwd = "/app/router.php?q=". base64_encode("/app/show-page.php");
        header('location: '.$fwd);
        exit(1);
    }
    
?>