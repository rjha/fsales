<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
     
    use com\indigloo\ui\form\Message as FormMessage;

    $gWeb = \com\indigloo\core\Web::getInstance();
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
        <title> Sign In</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top/site.inc'); ?>
        	<div class="row">
        		<div class="span8 offset1">
                    <?php FormMessage::render() ?>
                    <div class="page-header">
        			   <h3> Sign in</h3>
                    </div>
                    <div class="p10"> <?php FormMessage::render(); ?> </div>
                    <div class="p20">
                        <a href="<?php echo $fbDialogUrl; ?>" class="btn btn-large btn-success">Sign in using your facebook account -></a>
                    </div>

                    <div class="box-shadow p20 mt20">
                        <!-- image -->
                        <img src="/site/page/images/home-image_480_320.jpg" class="aligncenter" alt="main-image" />
                    </div>

                   
        		</div>
        	</div>
        </div>
        
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

