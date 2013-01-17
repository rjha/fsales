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
        $checkboxes = array();
        if(array_key_exists("p",$_POST)) {
            $checkboxes = $_POST["p"];
        }
        
        if(empty($checkboxes)) {
            $message = "You need to select a page!" ;
            $gWeb->store(Constants::FORM_ERRORS,array($message));
            $fwd =  AppConstants::SELECT_PAGE_URL ;

            header("location: ".$fwd);
            exit(1);

        }

        $pages = $gWeb->find("fs.user.pages",true);
        
        if(!empty($pages)) {
            $loginId = Login::getLoginIdInSession();
            $bucket = array();
            foreach($pages as $page) {
                if(in_array($page["id"],$checkboxes)) {
                    array_push($bucket,$page);
                }
            }

            // store selected pages in DB
            $streamDao = new \com\indigloo\fs\dao\Stream();
            $streamDao->addSources($loginId,$bucket);
        }

        $fwd = AppConstants::DASHBOARD_URL ;
        header("location: ".$fwd);
        exit(1);
        
    }catch(\Exception $ex) {
        
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());

        $message = "Error: something went wrong!" ;
        $gWeb->store("fs.router.message",$message);

        $params = array("q" => base64_encode(AppConstants::SELECT_PAGE_URL)) ;
        $fwd = Url::createUrl(AppConstants::ROUTER_URL,$params);
        
        header("location: ".$fwd);
        exit(1);
    }
    
?>