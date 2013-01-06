<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\api\Graph as GraphAPI ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $qparams = Url::getRequestQueryParams();
    
    $loginId = Login::getLoginIdInSession();
    $loginDao = new \com\indigloo\fs\dao\Login();
    $access_token = $loginDao->getValidToken($loginId);

    if(empty($access_token)) {
        $error = "Your session has expired. Please login again!";
        $errors = array($error);
        $gWeb->store(Constants::FORM_ERRORS,$errors);
        $qUrl = Url::tryBase64QueryParam("q", "/app/show-page.php");
        $fwd = '/app/browser/login.php?q='. $qUrl;
        header('location: '.$fwd);
        exit ;
    } 

    
    $pages = GraphAPI::getPages($access_token);
    // @todo - move constants to a separate file
    // fs is fabsales.com namespace.

    $gWeb->store("fs.user.pages",$pages);
    $pageHtml = \com\indigloo\fs\html\Page::getTable($pages);

?>

<!DOCTYPE html>
<html>

    <head>
        <title> User pages</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h2>Show page information</h2>
                    </div>
                    <p class="muted">
                    The application can manage the pages listed below.
                    Please click Confirm to continue.
                    </p>

                    <?php echo $pageHtml ; ?>
                    <div class="section">
                        <a class="btn btn-primary" href="/app/store-page.php">Confirm</a>
                        &nbsp;
                        <a class="btn" href="/app/remove-session-page.php">Cancel</a>
                    </div>
                </div>

            </div>

            
        </div>        

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>