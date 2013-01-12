<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Stream {

        function addSources($loginId,$pages) {
            mysql\Stream::addSources($loginId,$pages);
         }

        function getSources() {
           $rows = mysql\Stream::getSources();
           return $rows ;
        }

        function add($sourceId,$last_ts,$fbPosts) {
           mysql\Stream::add($sourceId,$last_ts,$fbPosts);
        }

        function get($limit) {
            $rows = mysql\Stream::get($limit);
            return $rows ;
        }

    }
}

?>