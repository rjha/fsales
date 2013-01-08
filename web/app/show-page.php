<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\ui\form\Message as FormMessage ;
    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\api\Graph as GraphAPI ;

    use \com\indigloo\fs\html\Application as AppHtml ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $qparams = Url::getRequestQueryParams();
    
    $loginId = Login::getLoginIdInSession();
    $loginDao = new \com\indigloo\fs\dao\Login();
    $access_token = $loginDao->getValidToken($loginId);

    if(empty($access_token)) {
        $error = "Your session has expired. Please login again!";
        $errors = array($error);
        $gWeb->store(Constants::FORM_MESSAGES,$errors);
       
        $fwd = "/app/browser/login.php" ;
        header("location: ".$fwd);
        exit ;
    } 

    $pages = GraphAPI::getPages($access_token);
    // @todo - move constants to a separate file
    // fs is fabsales.com namespace.
    $gWeb->store("fs.user.pages",$pages);
    $pageTableHtml = AppHtml::getPageTable($pages);

    $template = empty($pages) ? "/app/no-page.php" : "/app/page-table.php";
    $template = APP_WEB_DIR.$template ;

    $selfUrl = Url::current();
    include($template);
?>