<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    class Source {

        static function getAll($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");
            
            $sql = "select * from fs_source where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getDefault($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");
            
            $sql = "select * from fs_source where login_id = %d and is_default = 1 " ;
            $sql = sprintf($sql,$loginId);
            
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnId($sourceId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            //@todo - input check
            
            $sql = "select * from fs_source where source_id = '%s' " ;
            $sql = sprintf($sql,$sourceId);
            
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function makeDefault($loginId, $sourceId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            $sql = " update fs_source set is_default = 1 where login_id = %d and source_id = '%s' " ;
            $sql = sprintf($sql,$loginId,$sourceId);
            
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("is",$loginId, $sourceId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

    }
}

?>
