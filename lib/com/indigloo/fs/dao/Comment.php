<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Comment {

        function add($sourceId,$postId,$fbComments) {
            mysql\Comment::add($sourceId,$postId,$fbComments);
        }
    }
}

?>
