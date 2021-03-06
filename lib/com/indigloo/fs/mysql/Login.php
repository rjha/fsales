<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    class Login {

        static function getValidTokenOnSource($sourceId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($sourceId, "integer");

            $sql = " select access_token from fs_login where id = ".
                " (select login_id from fs_source where source_id = '%s' ) ".
                " and expire_on > (now() + interval 30 MINUTE ) " ;
            $sql = sprintf($sql,$sourceId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getValidToken($loginId) {
            // @todo - read from config file
            // a valid token should have 24 more hours to go!
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");

            $sql = "select access_token from fs_login where id = %d and expire_on > (now() + interval 30 MINUTE )" ;
            $sql = sprintf($sql,$loginId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

        static function getToken($loginId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");
            
            $sql = "select access_token from fs_login where id = %d " ;
            $sql = sprintf($sql,$loginId);
            
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

        static function updateTokenIp($sessionId,$loginId, $access_token, $expires,$remoteIp) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " update fs_login set access_token = ? , expire_on = %s, " ;
            $sql .= " ip_address = ?, session_id = ? , updated_on = now() where id = ? " ;
            $expiresOn = "(now() + interval ".$expires. " second)";
            $sql = sprintf($sql,$expiresOn);
            
            $stmt = $mysqli->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("sssi",$access_token,$remoteIp,$sessionId,$loginId);
                $stmt->execute();

                if ($mysqli->affected_rows != 1) {
                    MySQL\Error::handle($stmt);
                }
                $stmt->close();
            } else {
                MySQL\Error::handle($mysqli);
            }

        }

        static function getSources($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");
            
            $sql = "select * from fs_source where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

    }
}

?>
