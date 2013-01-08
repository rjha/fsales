<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Comment {

        function add($sourceId,$postId,$ts1,$fbComments) {
            mysql\Comment::add($sourceId,$postId,$ts1,$fbComments);
        }
    }
}

?>
