<?php 

    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\ui\form\Sticky;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
   
    
    $fbAppId = Config::getInstance()->get_value("facebook.app.id");
    $host = Url::base();
    $callbackUrl = $host."/app/canvas/index.php" ;
    
    //Ask for relevant permissions
    $authDialogUrl = "http://www.facebook.com/dialog/oauth?client_id=".
                    $fbAppId.
                    "&scope=email,manage_pages,publish_stream&redirect_uri=" . 
                    urlencode($callbackUrl);

    $signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    if (empty($data["user_id"])) {
        echo("<script> top.location.href='" . $auth_url . "'</script>");
    } else {
        $facebookId = $data["user_id"];
        header("Location: /app/canvas/test.php?user_id=".$facebookId);
    }


?>