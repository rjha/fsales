<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    class Source {

        static function getOnLogin($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");
            
            $sql = "select * from fs_source where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getOnId($sourceId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            //@todo - input check
            
            $sql = "select * from fs_source where source_id = '%s' " ;
            $sql = sprintf($sql,$sourceId);
            
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

    }
}

?>
