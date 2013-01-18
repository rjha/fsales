<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use com\indigloo\Constants as Constants;
    use \com\indigloo\Logger as Logger;

    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\mysql as mysql ;


    try{

        $access_token = NULL ;
        if(array_key_exists("token", $_POST)) {
            $access_token = $_POST["token"] ;
        }

        if(!empty($access_token)) {
            //copy from graph API explorer
            // make sure you have manage_pages/publish_stream/email permissions
            
            $facebookId = "100000110234029" ;

            $name = "Rajeev Jha" ;
            $firstName = "Rajeev" ;
            $lastName = "Jha" ;
            $email ="jha.rajeev@gmail.com" ;
            
            // we need to ensure that expiry > what is in valid token check
            // otherwise our code goes nuts!
            // @see http://developers.facebook.com/docs/howtos/login/extending-tokens/
            // facebook short lived token should be valid for 1-2 HR
            // our code validation is for 30 minutes 
            // so lets put the expire_on after 1 HR
            $expires = 1*3600 ;
            
            $facebookDao = new \com\indigloo\fs\dao\Facebook();
            $data = $facebookDao->getOrCreate($facebookId,
                $name,
                $firstName,
                $lastName,
                $email,
                $access_token,
                $expires);

            $loginId = $data["loginId"];
            
            // success - update login record
            // start a session
            $remoteIp = \com\indigloo\Url::getRemoteIp();
            mysql\Login::updateTokenIp(session_id(),$loginId,$access_token,$expires,$remoteIp);
            $code = Login::startOAuth2Session($loginId,$name);

            $message = sprintf("login success with token : %s ", $access_token);

        } else {
            $message = "No token : enter a token please!" ;
        }

        
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
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


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
                    <form action="/app/hack/login.php" method="POST">
                        Token : <input type="text" name ="token" value="" style="width:600px;"/>
                        <button class="btn" type="submit" name="submit" value="submit"> Submit</button>
                    </form>
                    
                </div>
            </div>
        </div>
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>