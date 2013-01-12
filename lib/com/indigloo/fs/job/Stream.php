<?php

namespace com\indigloo\fs\job {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\fs\dao as Dao ;
    use \com\indigloo\fs\api\Graph as GraphAPI;

    class Stream {

        
        static function execute() {
            // get all the sources 
            $streamDao = new Dao\Stream();
            $sources = $streamDao->getSources();

            foreach($sources as $source) {
                self::process($source);
            }
        }

        static function process($source) {
            $sourceId = $source["source_id"];
            $last_ts = $source["last_stream_ts"];
            $loginId = $source["login_id"];
            
            // @todo offset and LIMIT to graph API
            // right now we are fetching top 25 posts from source feed
            // if more than 25 posts have been touched (yes! a possibility)
            // then we will miss the 26th onwards.
            // issue#1 : (updated_time > ts) condition does not work for FQL
            // timestamp condition works for created_time only
            // 
            // issue #2 : with graph API (/page/feed?since=ts) also we 
            // have to page using nextURL. Ideally we should use graph API
            // and paginate to grab all the posts since stream.last_ts
            // 
            // token is page token and should not expire

            $token = $source["token"];
            $fbPosts = GraphAPI::getStreamViaFQL($sourceId,$last_ts,$token);

            if(Config::getInstance()->is_debug()) {
                $message = "\n poke stream :: source = %s, ts = %s, new posts = %d" ;
                $message = sprintf($message,$source["name"],$last_ts,sizeof($fbPosts));
                Logger::getInstance()->debug($message);
            }

            if(empty($fbPosts)) { return ; }

            $streamDao = new Dao\Stream();
            $streamDao->add($sourceId,$last_ts,$fbPosts);

        }

    }

}

?>
