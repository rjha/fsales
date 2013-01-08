<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Login {

        function getToken($loginId) {
            $row = mysql\Login::getToken($loginId);
            $token = empty($row) ? NULL : $row["access_token"];
            return $token ;
        }

        function getValidToken($loginId) {
            $row = mysql\Login::getValidToken($loginId);
            $token = empty($row) ? NULL : $row["access_token"];
            return $token ;
        }
        
        function getValidTokenOnSource($sourceId) {
            $row = mysql\Login::getValidTokenOnSource($sourceId);
            $token = empty($row) ? NULL : $row["access_token"];
            return $token ;
        }

        function getSources($loginId) {
            $rows = mysql\Login::getSources($loginId);
            return $rows ;
        }

    }
}

?>
