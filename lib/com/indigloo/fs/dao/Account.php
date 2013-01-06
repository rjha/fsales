<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Account {

        function addPages($loginId,$pages) {
            mysql\Account::addPages($loginId,$pages);
        }

    }
}

?>
