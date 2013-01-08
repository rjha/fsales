<?php

namespace com\indigloo\fs\job {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;

    use \com\indigloo\fs\dao as Dao ;
    use \com\indigloo\fs\api\Graph as GraphAPI;

    class Stream {

        
        static function execute() {
            // get all the sources 
            $streamDao = new Dao\Stream();
            $sources = $streamDao->getSources();

            foreach($sources as $source) {
                $sourceId = $source["source_id"];
                $ts = $source["last_stream_ts"];
                $loginId = $source["login_id"];
                $loginDao = new Dao\Login();
                $token = $loginDao->getValidToken($loginId);

                // @todo : error if token is stale
                // write to error_log table
                // show on user dashboard and admin dashboard
                // get latest photos in stream using graph API

                $fbPhotos = GraphAPI::getStreamPhotos($sourceId,$ts,$token);
                $streamDao->addPhotos($sourceId,$ts,$fbPhotos);
                
            }
        }

    }

}

?>
