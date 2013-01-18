<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Facebook {

        function getOrCreate($facebookId,
            $name,
            $firstName,
            $lastName,
            $email,
            $access_token,
            $expires) {

            // input check
            if(empty($facebookId)) {
                $message = "Bad input : No facebook id for user!" ;
                trigger_error($message,E_USER_ERROR);
            }

            if(empty($access_token)) {
                $message = "Bad input : access token is missing!" ;
                trigger_error($message,E_USER_ERROR);
            }

            $data = array();
            $loginId = NULL ;

            //is existing record?
            $facebookId = trim($facebookId);
            $remoteIp =  \com\indigloo\Url::getRemoteIp();
            $row = $this->getOnFacebookId($facebookId);

            if(empty($row)){
                $message = sprintf("Login::Facebook::create id %s, email %s ",$facebookId,$email);
                Logger::getInstance()->info($message);
                $source = 1 ;

                $loginId = mysql\Facebook::create(
                    $facebookId,
                    $name,
                    $firstName, 
                    $lastName,
                    $email,
                    $source,
                    $access_token,
                    $expires,
                    $remoteIp);

                $data["loginId"] = $loginId;
                // A new user has not selected his 
                // facebook pages yet!
                $data["select_page"] = true ;
                

            } else {
                // existing  facebook user
                // op_bit = 1 is for user who have not 
                // selected their facebook pages yet.
                // op_bit = 2 is for users who have selected their 
                // facebook pages.
                $loginId = $row["login_id"];
                $op_bit  = $row["op_bit"];

                $data["loginId"] = $loginId;
                $data["select_page"] = ($op_bit == 1 ) ? true : false ;
                
            }

            return $data ;

        }

        function getOnFacebookId($facebookId) {
            $row = mysql\Facebook::getOnFacebookId($facebookId);
            return $row ;
        }

        function getOnLoginId($loginId) {
            $row = mysql\Facebook::getOnLoginId($loginId);
            return $row ;
        }
        
    }
}

?>
