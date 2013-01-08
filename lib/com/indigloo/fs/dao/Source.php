<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Source {

        function getOnLogin($loginId) {
            $rows = mysql\Source::getOnLogin($loginId);
            return $rows ;
        }

        function getOnId($sourceId) {
            $row = mysql\Source::getOnId($sourceId);
            return $row ;
        }


    }
}

?>
