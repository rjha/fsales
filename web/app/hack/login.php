<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use com\indigloo\Constants as Constants;
    use \com\indigloo\Logger as Logger;

    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\mysql as mysql ;


    try{

        //copy from graph API explorer
        // make sure you have manage_pages/publish_stream/email permissions
        $access_token = "AAACEdEose0cBAMKFlZAqii3AnMrV7XlYlC4MGly487tSu2xJsrpZAEDz0b6tyea7vxiNYlMm1UF0E8vFhHlNxicAz5vDZAthqljYAOTGXnBGheBAGAI" ;
        $facebookId = "100000110234029" ;

        $name = "Rajeev Jha" ;
        $firstName = "Rajeev" ;
        $lastName = "Jha" ;
        $email ="jha.rajeev@gmail.com" ;
        // although we get 1 HR token from graph API explorer
        // we need to ensure that expiry > 1 DAY
        // otherwise our code goes nuts!
        $expires = 2*24*3600 ;
        
        $facebookDao = new \com\indigloo\fs\dao\Facebook();
        $data = $facebookDao->getOrCreate($facebookId,
            $name,
            $firstName,
            $lastName,
            $email,
            $access_token,
            $expires);

        $loginId = $data["loginId"];
        $signup = $data["signup"];
        
        // success - update login record
        // start a session
        $remoteIp = \com\indigloo\Url::getRemoteIp();
        mysql\Login::updateTokenIp(session_id(),$loginId,$access_token,$expires,$remoteIp);
        $code = Login::startOAuth2Session($loginId,$name);

        $message = "login success";
        
    }catch(\Exception $ex) {
        
        Logger::getInstance()->error($ex->getMessage());
        Logger::getInstance()->backtrace($ex->getTrace());
        $message = $ex->getMessage();
        
    }
    
?>

<!DOCTYPE html>
<html>

    <head>
        <title> Login Hack</title>
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
                        <h2>Login Hack</h2>
                    </div>
                    <p class="comment-text"> <?php echo $message; ?> </p>
                </div>
            </div>
        </div>
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>