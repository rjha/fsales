<?php

function app_browser_errors($errorno,$errorstr,$file,$line) {

    if(error_reporting() == 0 ) {
        // do nothing for silenced errors
        return true ;
    }
    
    switch($errorno) {

        case E_STRICT :
            return true;
        case E_NOTICE :
        case E_USER_NOTICE :
            \com\indigloo\Logger::getInstance()->error(" $file :: $line :: $errorstr");
            break ;

        default:
            \com\indigloo\Logger::getInstance()->error(" $file :: $line :: $errorstr");
            \com\indigloo\Logger::getInstance()->backtrace();

            $message = "something went wrong with the sign-in. please try again" ;
            $_SESSION["form.errors"] = array($message);
            $location = "/ghost/canvas/login-error" ;

            //running inside facebook canvas?
            $uiMode = NULL ;
            if( isset($_SESSION["global.ui.mode"]) 
                && !empty($_SESSION["global.ui.mode"])
                && (strcmp($_SESSION["global.ui.mode"], "canvas") == 0 )) {
                
                $location = "http://apps.facebook.com/favsales/login-error";
            }


            header('Location: '.$location);
            exit(1);
            
    }
    
    //do not execute PHP error handler
    return true ;
}

?>