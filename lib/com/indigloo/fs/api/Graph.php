<?php

namespace com\indigloo\fs\api {

    
    use \com\indigloo\Util as CoreUtil;
    use \com\indigloo\Logger as Logger;
    use \com\indigloo\Url ;

    class Graph {

    	static function isValidResponse($graphUrl,$fbObject,$attributes=NULL) {
    		$flag = true ;

    		// FACEBOOK GRAPH API can return true | false 
            // php json_decode can return TRUE | FALSE | NULL
           
    		if($fbObject === FALSE || $fbObject ===  TRUE || $fbObject == NULL ) {
                //@todo more instrumentation
                $graphUrl = urldecode($graphUrl);
                $message = sprintf("Graph URL [%s] returned TRUE|FALSE|NULL",$graphUrl) ;
                Logger::getInstance()->error($message);
                $flag = false ;
                return $flag ;
            }

            if(is_object($fbObject) && property_exists($fbObject, "error")) { 
                $message = sprintf("Graph URL [%s] returned error",$graphUrl) ;
                Logger::getInstance()->error($message);
                Logger::getInstance()->error($fbObject->error);
                $flag = false ;
                return $flag ;
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
            $response = @file_get_contents($graphAPI,false,$context);
            $fbObject = json_decode($response);

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

        	$pages = array();

        	CoreUtil::isEmpty("Access token", $token);
            $params = array("access_token" => $token);
            
            // use https when passing access tokens
            $graphAPI = "https://graph.facebook.com/me/accounts" ;
            $graphUrl = Url::createUrl($graphAPI,$params);
            
            $response = @file_get_contents($graphUrl);
            $fbObject = json_decode($response);
           
            $attributes = array("data");
            if(!self::isValidResponse($graphUrl,$fbObject,$attributes)) {
            	return $pages ;
            }
           	
            $accounts = $fbObject->data ;
            foreach($accounts as $account) {
            	$page = array();
            	$page["id"] = $account->id ;
            	$page["access_token"] = $account->access_token ;
            	$page["name"] = $account->name ;
            	$pages[] = $page ;
            	 
            }

            return $pages ;
        }

        static function getStreamViaFQL($sourceId,$ts,$token) {

            $photos = array();

            // @todo check type = 247 for photos 
            // there were photots but type was null
            // so adding photo = 247 does not work!
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

            $fql = " select post_id, updated_time from stream where source_id = %s ".
                    " order by updated_time DESC LIMIT 25 OFFSET 0 " ;

            $fql = sprintf($fql,$sourceId,$ts);

            //fire FQL
            $graphAPI = "https://graph.facebook.com/fql" ;
            $params = array("q" => urlencode($fql), "access_token" => $token);
            $graphUrl = Url::createUrl($graphAPI,$params);
            
            $response = @file_get_contents($graphUrl);
            $fbObject = json_decode($response);
            
            $attributes = array("data");
            if(!self::isValidResponse($graphUrl,$fbObject,$attributes)) {
                return $photos ;
            }

            $posts = $fbObject->data ;
            $last_stream_ts = (int) $ts ;
            
            foreach($posts as $post) {
                $photo_ts =  (int) $post->updated_time; 
                if($photo_ts <= $last_stream_ts) {
                    break ;
                }

                $photo = array();
                $photo["post_id"] = $post->post_id ;
                $photo["updated_time"] = $post->updated_time;

                $photos[] = $photo ;
            }

            return $photos ;

        }

        static function getPost($postId,$token) {
            
            $post = array();

            CoreUtil::isEmpty("Access token", $token);
            $params = array("access_token" => $token, "fields" => "picture,link,object_id,message");
            
            // use https when passing access tokens
            $graphAPI = "https://graph.facebook.com/%s" ;
            $graphAPI = sprintf($graphAPI,$postId);

            $graphUrl = Url::createUrl($graphAPI,$params);
            $response = @file_get_contents($graphUrl);
            $fbObject = json_decode($response);
           
             
            if(!self::isValidResponse($graphUrl,$fbObject)) {
                return $post ;
            }
            
            $post["picture"] = $fbObject->picture ;
            $post["link"] = $fbObject->link ;
            $post["object_id"] = $fbObject->object_id ;
            $post["message"] = $fbObject->message ;

            return $post ;
           
        }

        static function getCommentsViaFQL($objectId,$ts1,$limit,$token) {
            $comments = array();

            $fql = " select fromid, text, username, time from comment ".
                " where object_id = %s ".
                " and time >= %s ".
                " order by time limit %s " ;

            $fql = sprintf($fql,$objectId,$ts1,$limit);
            
            //fire FQL
            $graphAPI = "https://graph.facebook.com/fql" ;
            $params = array("q" => urlencode($fql), "access_token" => $token);
            $graphUrl = Url::createUrl($graphAPI,$params);
            
            $response = @file_get_contents($graphUrl);
            $fbObject = json_decode($response);
            
            $attributes = array("data");
            if(!self::isValidResponse($graphUrl,$fbObject,$attributes)) {
                return $comments ;
            }

            $fbComments = $fbObject->data ;
            
            foreach($fbComments as $fbComment) {
                $comment = array();
                $comment["user_name"] = $fbComment->username ;
                $comment["from_id"] = $fbComment->fromid ;
                $comment["message"] = $fbComment->text ;
                $comment["created_time"] = $fbComment->time ;
                $comments[] = $comment ;
            } 

            return $comments ;
           
        }

        static function getComments($postId,$ts1,$limit,$token) {
            $comments = array();

            $graphAPI = "https://graph.facebook.com/%s/comments" ;
            $graphAPI = sprintf($graphAPI,$postId);

            $params = array("date_format" => "U",
                            "limit" => $limit,
                            "since" => $ts1,
                            "access_token" => $token);
            $graphUrl = Url::createUrl($graphAPI,$params);
            
            $response = @file_get_contents($graphUrl);
            $fbObject = json_decode($response);
            
            $attributes = array("data");
            if(!self::isValidResponse($graphUrl,$fbObject,$attributes)) {
                return $comments ;
            }

            $fbComments = $fbObject->data ;
            
            foreach($fbComments as $fbComment) {
                $comment = array();
                
                $comment["comment_id"] = $fbComment->id ;
                $comment["user_name"] = $fbComment->from->name ;
                $comment["from_id"] = $fbComment->from->id ;
                $comment["message"] = $fbComment->message ;
                $comment["created_time"] = $fbComment->created_time ;

                $comments[] = $comment ;
            } 

            return $comments ;
           
        }


    }
}

?>