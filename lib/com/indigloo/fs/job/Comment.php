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

            $sourceDao = new Dao\Source();
            // source access token
            $token = $sourceDao->getToken($sourceId);
            $postDao = new Dao\Post();

            if(!$postDao->exists($postId)) {
                $fbResponse = GraphAPI::getPost($postId,$token);
                $fbCode = $fbResponse["code"];
                settype($fbCode, "integer");
                if($fbCode == AppConstants::SERVER_ERROR_CODE) {
                    return ;
                }

                $fbPost = $fbResponse["data"];
                $postDao->add($sourceId,$postId,$fbPost);

                if(Config::getInstance()->is_debug()) {
                    $message = sprintf("fs_post :: fetch post_id :: %s ",$postId);
                    Logger::getInstance()->debug($message);
                }
            }
            
            self::pull_comments($sourceId,$postId,$last_ts,$version,$token);
            
        }
        
        static function pull_comments($sourceId,$postId,$last_ts,$version,$token) {
            // pull N comments using FQL sorted by created_time
            $limit = 20 ;
            $fbResponse =  GraphAPI::getComments($postId,$last_ts,$limit,$token);
            $fbCode = $fbResponse["code"];
            settype($fbCode, "integer");
            if($fbCode == AppConstants::SERVER_ERROR_CODE) {
                return ;
            }

            $fbComments = $fbResponse["data"];
            if(empty($fbComments)) {
                return ;
            }

            if(Config::getInstance()->is_debug()) {
                $message = " fs_stream :: post %s , last_ts = %s , version = %d, no_comments =  %d ";
                $message = sprintf($message,$postId,$last_ts,$version,sizeof($fbComments));
                Logger::getInstance()->debug($message);
            }

            $commentDao = new Dao\Comment();
            $commentDao->add($sourceId,$postId,$last_ts,$version,$limit,$fbComments);

        }
    }
}

?>
