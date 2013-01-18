<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Comment {

        function add($sourceId,$postId,$last_ts,$version,$limit,$fbComments) {
            mysql\Comment::add($sourceId,$postId,$last_ts,$version,$limit,$fbComments);
        }

        function getPaged($sourceId,$ft,$paginator) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($sourceId,$ft,$limit);
            } else {
                $params = $paginator->getDBParams();
                $start = $params["start"];
                $direction = $params["direction"];
                $rows = mysql\Comment::getPaged($sourceId,$ft,$start,$direction,$limit);
                return $rows ;
            }

        }

        function getLatest($sourceId,$ft,$limit) {
            $rows = mysql\Comment::getLatest($sourceId,$ft,$limit);
            return $rows ;
        }

        function getOnId($commentId) {
            $row = mysql\Comment::getonId($commentId);
            return $row ;
        }

    }
}

?>
