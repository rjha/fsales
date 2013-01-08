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

        function addPhotos($sourceId,$ts,$photos) {
           mysql\Stream::addPhotos($sourceId,$ts,$photos);
        }

        function getPosts($limit) {
            $rows = mysql\Stream::getPosts($limit);
            return $rows ;
        }

        function getLastTS($postId) {
            $row = mysql\Stream::getLastTS($postId);
            $ts = empty($row) ? NULL : $row["last_stream_ts"];
            return $ts ;
        }

    }
}

?>