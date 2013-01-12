<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Post {

        function exists($postId) {
            $row = mysql\Post::getOnId($postId);
            $flag = empty($row) ? false : true ;
            return $flag ;
        }

        function getObjectId($postId) {
            $row = mysql\Post::getObjectId($postId);
            $objectId = empty($row) ? NULL : $row["object_id"];
            return $objectId ;
        }

        function add($sourceId,$postId,$fbPost) {
            mysql\Post::add($sourceId,$postId,$fbPost);
        }
    }
}

?>
