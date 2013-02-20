<?php

namespace com\indigloo\fs\job {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\fs\dao as Dao ;
    use \com\indigloo\fs\api\Graph as GraphAPI;
    use \com\indigloo\fs\Constants as AppConstants ;


    class Comment {

        static function execute() {
            
            $streamDao = new Dao\Stream();
            // @todo - paginate on posts
            $limit = 25 ;
            $streams = $streamDao->get($limit);

            foreach($streams as $stream) {
                self::process($stream);
            }

        }

        static function process($stream) {

            $postId = $stream["post_id"];
            $sourceId = $stream["source_id"];
            $last_ts = $stream["last_stream_ts"];
            $version = $stream["version"] ;

            if(Config::getInstance()->is_debug()) {
                $message = "comment_job::fs_stream: source_id= %s,post_id= %s, last_stream_ts= %s";
                $message = sprintf($message,$sourceId,$postId,$last_ts);
                Logger::getInstance()->debug($message);
                Logger::getInstance()->debug("--------------------------");
            }

            $sourceDao = new Dao\Source();
            // source access token
            $token = $sourceDao->getToken($sourceId);
            $postDao = new Dao\Post();

            if(!$postDao->exists($postId)) {

                if(Config::getInstance()->is_debug()) {
                    $message = sprintf("comment_job::create post:: post_id= %s ",$postId);
                    Logger::getInstance()->debug($message);
                }

                $fbResponse = GraphAPI::getPost($postId,$token);
                $fbCode = $fbResponse["code"];
                settype($fbCode, "integer");

                if($fbCode == AppConstants::SERVER_ERROR_CODE) {
                    // we can get *bad* posts and these bad posts 
                    // will never be deleted from fs_stream.
                    // As a result we will never go to comment loading 
                    // and cleaning stage. it is better to mark such posts 
                    // and not load them into stream the next time.
                    $streamDao = new Dao\Stream();
                    $streamDao->setState($sourceId,$postId,2);
                    return ;
                }

                $fbPost = $fbResponse["data"];
                $postDao->add($sourceId,$postId,$fbPost);

            }
            
            self::pull_comments($sourceId,$postId,$last_ts,$version,$token);
            
        }
        
        static function pull_comments($sourceId,$postId,$last_ts,$version,$token) {
            if(Config::getInstance()->is_debug()) {
                $message = "comment_job:: pull comments : post_id= %s, last_stream_ts= %s";
                $message = sprintf($message,$postId,$last_ts);
                Logger::getInstance()->debug($message);
            }

            // pull N comments using FQL sorted by created_time
            $limit = 20 ;
            $fbResponse =  GraphAPI::getComments($postId,$last_ts,$limit,$token);
            $fbCode = $fbResponse["code"];
            settype($fbCode, "integer");

            if($fbCode == AppConstants::SERVER_ERROR_CODE) {
                $message = "comment_job:: error fetching comments for source =%s, post= %s" ;
                $message = sprintf($message,$sourceId,$postId);
                Logger::getInstance()->error($message);
                //first fix the error.?
                return ;
            }

            $fbComments = $fbResponse["data"];

            /*
             * __DO_NOT_RETURN_ from here
             * we need to delete stream rows when we do not find any comments 
             * for a given post_id and between(last_stream_ts,next_stream_ts)

              if(empty($fbComments)) { return ; } */


            $commentDao = new Dao\Comment();
            $commentDao->add($sourceId,$postId,$last_ts,$version,$limit,$fbComments);

            if(Config::getInstance()->is_debug()) {
                $message = "comment_job:: post_id =%s, last_stream_ts= %s, version = %d, num_comments= %d ";
                $message = sprintf($message,$postId,$last_ts,$version,sizeof($fbComments));
                Logger::getInstance()->debug($message);
                Logger::getInstance()->debug("--------------------------");

            }

        }
    }
}

?>
