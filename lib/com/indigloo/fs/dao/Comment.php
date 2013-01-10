<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Comment {

        function add($sourceId,$postId,$ts1,$fbComments) {
            mysql\Comment::add($sourceId,$postId,$ts1,$fbComments);
        }

        function getPaged($sourceId,$paginator) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($sourceId,$limit);
            } else {
                $params = $paginator->getDBParams();
                $start = $params["start"];
                $direction = $params["direction"];
                $rows = mysql\Comment::getPaged($sourceId,$start,$direction,$limit);
                return $rows ;
            }

        }

        function getLatest($sourceId,$limit) {
            $rows = mysql\Comment::getLatest($sourceId,$limit);
            return $rows ;
        }

        function getOnId($commentId) {
            $row = mysql\Comment::getonId($commentId);
            return $row ;
        }

    }
}

?>
