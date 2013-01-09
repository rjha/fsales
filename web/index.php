<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\fs\auth\Login as Login ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $qparams = Url::getRequestQueryParams();
    $loginId = Login::tryLoginIdInSession();

    //sign up information
    $stoken = Util::getMD5GUID();
    $gWeb->store("fb_state_token",$stoken);

    $fbAppId = Config::getInstance()->get_value("facebook.app.id");
    $host = Url::base();
    $fbCallback = $host."/app/browser/login-router.php" ;

    $fbDialogUrl = "https://www.facebook.com/dialog/oauth?client_id=".$fbAppId ;
    $fbDialogUrl .= "&redirect_uri=".urlencode($fbCallback)."&scope=email,manage_pages,publish_stream&state=".$stoken ;


?>

<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo G_APP_TAGLINE ?></title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                     
                    <div style="padding-top:100px;">
                        &nbsp;
                    </div>
                    
                    <p class="lead">
                        To get started, sign up using your facebook account now!
                    </p>
                    <div class="p20">
                        <a href="<?php echo $fbDialogUrl; ?>" class="btn btn-large btn-success">Sign up</a>
                    </div>
                </div>

            </div>

           

        </div>        

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

