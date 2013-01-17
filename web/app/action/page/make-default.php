<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');

    use \com\indigloo\Constants as Constants;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\fs\auth\Login as Login ;

    use \com\indigloo\fs\Constants as AppConstants ;

    try{

        $gWeb = \com\indigloo\core\Web::getInstance();
        $sourceId = NULL ;

        if(array_key_exists("source_id", $_POST)) {
            $sourceId = $_POST["source_id"];
        }

        if(!empty($sourceId)) {
            //make default
            $loginId = Login::getLoginIdInSession();
            $sourceDao = new \com\indigloo\fs\dao\Source();
            $sourceDao->makeDefault($loginId,$sourceId);

        } else {
            $message = "Error: we did not find a valid page!" ;
            $gWeb->store(Constants::FORM_ERRORS, array($message));
        }

        $message = "success! default page assigned." ;

        $gWeb->store(Constants::FORM_MESSAGES, array($message));
        $fwd = AppConstants::DASHBOARD_URL ;
        header("location: ".$fwd);
        exit(1);
       
        
    }catch(\Exception $ex) {
        
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $message = "Error: could not assign default page." ;
        $gWeb->store(Constants::FORM_ERRORS, array($message)) ;
        $fwd = AppConstants::DASHBOARD_URL ;
        header("location: ".$fwd);
        exit(1);
    }
    
?>