<?php

namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;
    use \com\indigloo\fs\Constants as AppConstants ;

    class Preference {

        static function get($key) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $key = $mysqli->real_escape_string($key);

            $sql = " select t_value as value from fs_hash_table where t_key = '%s' ";
            $sql = sprintf($sql,$key);
            
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function set($key,$value) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " insert into fs_hash_table(t_key,t_value) values(?,?) " ;
            $sql .= " ON DUPLICATE KEY update t_value = values(t_value) ";

            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ss",$key,$value);
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