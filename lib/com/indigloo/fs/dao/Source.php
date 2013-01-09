<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Source {

        function getAll($loginId) {
            $rows = mysql\Source::getAll($loginId);
            return $rows ;
        }

        function getOnId($sourceId) {
            $row = mysql\Source::getOnId($sourceId);
            return $row ;
        }

        function getDefault($loginId) {
            $row = mysql\Source::getDefault($loginId);
            $sourceId = (empty($row) ) ? NULL : $row["source_id"];
            return $sourceId ;
        }

        function makeDefault($loginId,$sourceId) {
            mysql\Source::makeDefault($loginId,$sourceId) ;
        }

    }
}

?>
