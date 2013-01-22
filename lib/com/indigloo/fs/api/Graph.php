<?php

namespace com\indigloo\fs\api {

    
    use \com\indigloo\Util as CoreUtil;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\Url ;
    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\fs\Constants as AppConstants ;

    class Graph {

        static function isValidResponse($graphUrl,$fbObject,$attributes=NULL) {
            $flag = true ;

            // FACEBOOK GRAPH API can return true | false 
            // php json_decode can return TRUE | FALSE | NULL
           
            if($fbObject === FALSE || $fbObject ===  TRUE || $fbObject == NULL ) {
                $graphUrl = urldecode($graphUrl);
                $message = sprintf("Graph URL [%s] returned true/false/null ",$graphUrl) ;
                Logger::getInstance()->error($message);
                return false ;
            }

            if(is_object($fbObject) && property_exists($fbObject, "error")) { 
                $message = sprintf("Graph URL [%s] returned error ",$graphUrl) ;
                Logger::getInstance()->error($message);
                Logger::getInstance()->error($fbObject->error);
                return false ;
            }

            if(is_object($fbObject) && !empty($attributes)) {
                foreach($attributes as $attribute) {
                    if(!property_exists($fbObject,$attribute)) {
                        $flag = false ;
                        break ;
                    }
                }
            }
           
            return $flag ;
        }

        static function addAppToPage($appId, $pageId,$pageToken) {

            $flag = false ;

            $graphAPI = "https://graph.facebook.com/%s/tabs" ;
            $graphAPI = sprintf($graphAPI,$pageId);

            //Do a POST
            $postdata = http_build_query(array("app_id" => $appId, "access_token" => $pageToken)) ;

            $opts = array('http' =>
                array(
                    'method'  => "POST",
                    'header'  => "Content-type: application/x-www-form-urlencoded",
                    'content' => $postdata
                )
            );

            $context  = stream_context_create($opts);
            $apiResponse = @file_get_contents($graphAPI,false,$context);
            $fbObject = json_decode($apiResponse);

            // The API actually returns true| false so we cannot run this 
            // through our usual error checker!
            if(is_object($fbObject) && property_exists($fbObject, "error")) { 
                $message = sprintf(" Error: Not able to add App %s to page %s ",$appId, $pageId) ;
                Logger::getInstance()->error($message);
                Logger::getInstance()->error($fbObject->error);
                return false ;
            }

            return $fbObject ; 

        }

        static function getPages($token) {
            $response = array();                
            $response["code"] = AppConstants::SERVER_ERROR_CODE ;
            $response["data"] = array() ;

            CoreUtil::isEmpty("Access token", $token);
            $params = array("access_token" => $token);
            
            // use https when passing access tokens
            $graphAPI = "https://graph.facebook.com/me/accounts" ;
            $graphUrl = Url::createUrl($graphAPI,$params);
            
            $apiResponse = @file_get_contents($graphUrl);
            $fbObject = json_decode($apiResponse);
           
            $attributes = array("data");
            // error returned by graph API  
            if(!self::isValidResponse($graphUrl,$fbObject,$attributes)) { 
                return $response ; 
            }

            try{
                $pages = array();
                $accounts = $fbObject->data ;

                foreach($accounts as $account) {
                    
                    // property check
                    // facebook can return any weird thing 
                    // so lets guard against 1. E_NOTICE
                    // 2. required DB COLUMN

                    if(property_exists($account, "id")
                        && property_exists($account, "access_token")
                        && property_exists($account, "name")) {

                        $page = array();
                        $page["id"] = $account->id ;
                        $page["access_token"] = $account->access_token ;
                        $page["name"] = $account->name ;
                        $pages[] = $page ;
                    }
                    
                }

                $response["code"] = AppConstants::SERVER_OK_CODE ;
                $response["data"] = $pages ;

            } catch(\Exception $ex) {
                Logger::getInstance()->error($ex->getMessage());
            }

            return $response ;
        }

        static function getStreamViaFQL($sourceId,$ts,$token) {
            $response = array();
            $response["code"] = AppConstants::SERVER_ERROR_CODE ;
            $response["data"] = array() ;


            // @todo check type = 247 for photos 
            // for some photos returned type was NULL
            // so condition type = 247 does not work!
            // --------------------------------------------------------
            // capturing stream.updated_time > a_timestamp posts
            // ---------------------------------------------------------
            // for FQL queries on stream, conditions like 
            // updated_time > a_timestamp does not work. Only the created_time
            // timestamp comparison works. This is weird but in line with how 
            // Facebook wall/timeline works. The sorting on updated_time works though!
            // ---------------------------------------------------------
            // we use LIMIT and offset trick to scroll till the required timestamp
            // on updated_time column.
            // @todo implement pagination
            // 

            $fql =  " select post_id, updated_time from stream where source_id = %s ".
                    " order by updated_time DESC LIMIT 25 OFFSET 0 " ;

            $fql = sprintf($fql,$sourceId,$ts);

            //fire FQL
            $graphAPI = "https://graph.facebook.com/fql" ;
            $params = array("q" => urlencode($fql), "access_token" => $token);
            $graphUrl = Url::createUrl($graphAPI,$params);

            $apiResponse = @file_get_contents($graphUrl);
            $fbObject = json_decode($apiResponse);
            
            $attributes = array("data");
            if(!self::isValidResponse($graphUrl,$fbObject,$attributes)) {
                return $response ;
            }

            try{
                $photos = array() ;
                $posts = $fbObject->data ;
                $last_stream_ts = (int) $ts ;
                
                foreach($posts as $post) {
                    if(property_exists($post, "post_id")
                        && property_exists($post, "updated_time")) {

                        $photo_ts =  (int) $post->updated_time; 
                        if($photo_ts <= $last_stream_ts) {
                            break ;
                        }

                        $photo = array();
                        $photo["post_id"] = $post->post_id ;
                        $photo["updated_time"] = $post->updated_time;
                        $photos[] = $photo ;
                    }
                }

                $response["code"] = AppConstants::SERVER_OK_CODE ;
                $response["data"] = $photos ;


            } catch(\Exception $ex) {
                Logger::getInstance()->error($ex->getMessage());
            }

            return $response ;

        }

        static function getPost($postId,$token) {
            $response = array();
            $response["code"] = AppConstants::SERVER_ERROR_CODE ;
            $response["data"] = array() ;

            CoreUtil::isEmpty("Access token", $token);
            $params = array("access_token" => $token, "fields" => "picture,link,object_id,message");
            
            // use https when passing access tokens
            $graphAPI = "https://graph.facebook.com/%s" ;
            $graphAPI = sprintf($graphAPI,$postId);

            $graphUrl = Url::createUrl($graphAPI,$params);
            $apiResponse = @file_get_contents($graphUrl);
            $fbObject = json_decode($apiResponse);
           
             
            if(!self::isValidResponse($graphUrl,$fbObject)) {
                return $response ;
            }

            if(property_exists($fbObject, "object_id")
                && property_exists($fbObject, "message")) {

                $post = array();
                // @todo : placeholder pic /link
                $post["picture"] = property_exists($fbObject,"picture") ? $fbObject->picture : "";
                $post["link"] = property_exists($fbObject,"link") ? $fbObject->link : "";

                $post["object_id"] = $fbObject->object_id ;
                $post["message"] = $fbObject->message ;

                if(property_exists($fbObject, "from")) {
                    $post["from_id"] = $fbObject->from->id ;
                } else {
                    $post["from_id"] = "" ;
                }

                $response["code"] = AppConstants::SERVER_OK_CODE ;
                $response["data"] = $post ;
            }

            return $response ;
           
        }

        static function getCommentsViaFQL($objectId,$ts1,$limit,$token) {
            $response = array();
            $response["code"] = AppConstants::SERVER_ERROR_CODE ;
            $response["data"] = array() ;

            $fql = " select fromid, text, username, time from comment ".
                " where object_id = %s ".
                " and time >= %s ".
                " order by time limit %s " ;

            $fql = sprintf($fql,$objectId,$ts1,$limit);
            
            //fire FQL
            $graphAPI = "https://graph.facebook.com/fql" ;
            $params = array("q" => urlencode($fql), "access_token" => $token);
            $graphUrl = Url::createUrl($graphAPI,$params);
            
            $apiResponse = @file_get_contents($graphUrl);
            $fbObject = json_decode($apiResponse);
            
            $attributes = array("data");
            if(!self::isValidResponse($graphUrl,$fbObject,$attributes)) {
                return $response ;
            }

            try {
                $fbComments = $fbObject->data ;
                $comments = array();

                foreach($fbComments as $fbComment) {
                    if(property_exists($fbComment, "fromid")
                        && property_exists($fbComment, "text")
                        && property_exists($fbComment, "time")){
                        

                        $comment = array();
                        $comment["user_name"] = property_exists($fbComment,"username") ? 
                                                    $fbComment->username : "Anonymous";
                        $comment["from_id"] = $fbComment->fromid ;
                        $comment["message"] = $fbComment->text ;
                        $comment["created_time"] = $fbComment->time ;
                        $comments[] = $comment ;
                    }
                }

                $response["code"] = AppConstants::SERVER_OK_CODE ;
                $response["data"] = $comments ;

            } catch(\Exception $ex) {
                Logger::getInstance()->error($ex->getMessage());
            }

            return $response ;
           
        }

        static function getComments($postId,$ts1,$limit,$token) {
            $response = array();
            $response["code"] = AppConstants::SERVER_ERROR_CODE ;
            $response["data"] = array() ;

            $graphAPI = "https://graph.facebook.com/%s/comments" ;
            $graphAPI = sprintf($graphAPI,$postId);

            $params = array("date_format" => "U",
                            "limit" => $limit,
                            "since" => $ts1,
                            "access_token" => $token);
            $graphUrl = Url::createUrl($graphAPI,$params);
            
            $apiResponse = @file_get_contents($graphUrl);
            $fbObject = json_decode($apiResponse);
            
            $attributes = array("data");
            if(!self::isValidResponse($graphUrl,$fbObject,$attributes)) {
                return $response ;
            }


            try {
                $fbComments = $fbObject->data ;
                $comments = array() ;

                foreach($fbComments as $fbComment) {
                    if(property_exists($fbComment, "id")
                        && property_exists($fbComment, "message")
                        && property_exists($fbComment, "created_time")){
                        
                        $comment = array();
                        $comment["comment_id"] = $fbComment->id ;
                        $comment["message"] = $fbComment->message ;
                        $comment["created_time"] = $fbComment->created_time ;

                        if(property_exists($fbComment, "from")) {
                            $comment["user_name"] = $fbComment->from->name ;
                            $comment["from_id"] = $fbComment->from->id ;
                        } else {
                            $comment["user_name"] = "Anonymous";
                            $comment["from_id"] = "" ;
                        }
                           
                        $comments[] = $comment ;
                    }
                }

                $response["code"] = AppConstants::SERVER_OK_CODE ;
                $response["data"] = $comments ;

            } catch(\Exception $ex) {
                Logger::getInstance()->error($ex->getMessage());
            }

            return $response ;
           
        }


    }
}

?>