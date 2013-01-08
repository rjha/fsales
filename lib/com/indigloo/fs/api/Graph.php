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
                $message = sprintf("Graph URL [%s] returned TRUE|FALSE|NULL",$graphUrl) ;
                Logger::getInstance()->error($message);
                $flag = false ;
                return $flag ;
            }

            if(property_exists($fbObject, "error")) { 
                $message = sprintf("Graph URL [%s] returned error",$graphUrl) ;
                Logger::getInstance()->error($message);
                Logger::getInstance()->error($fbObject->error);
                $flag = false ;
                return $flag ;
            }

            if(!empty($attributes)) {
            	foreach($attributes as $attribute) {
            		if(!property_exists($fbObject,$attribute)) {
            			$flag = false ;
            			break ;
            		}
            	}
            }
           
            return $flag ;
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

        static function getStreamPhotos($sourceId,$ts,$token) {

            $photos = array();

            // @todo check type = 247 for photos 
            // there were photots but type was null(!!!)
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
                    " and type = 247 order by updated_time DESC LIMIT 25 OFFSET 0 " ;

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

        static function getPagePost($sourceId,$ts,$token) {

            $photos = array();

            // @todo check type = 247 for photos 
            // there were photots but type was null(!!!)
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
                    " and type = 247 order by updated_time DESC LIMIT 25 OFFSET 0 " ;

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


    }
}

?>