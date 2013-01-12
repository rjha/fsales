<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Invoice {

        function getOnId($invoiceId) {
            $row = mysql\Invoice::getonId($invoiceId);
            return $row ;
        }

        function create($loginId,
                        $commentId,
                        $name,
                        $email,
                        $unitPrice,
                        $quantity,
                        $seller_info) {

            $invoiceId =  mysql\Invoice::create($loginId,
                                            $commentId,
                                            $name,
                                            $email,
                                            $unitPrice,
                                            $quantity,
                                            $seller_info);
            return $invoiceId;
        }


                                  
    }
}

?>
