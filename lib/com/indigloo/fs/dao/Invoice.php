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

        function getOnId2($invoiceId) {
            $row = mysql\Invoice::getonId2($invoiceId);
            return $row ;
        }

        function getPaged($loginId,$paginator) {
            $limit = $paginator->getPageSize();
            
            if($paginator->isHome()){
                return $this->getLatest($loginId,$limit);
            } else {
                $params = $paginator->getDBParams();
                $start = $params["start"];
                $direction = $params["direction"];
                $rows = mysql\Invoice::getPaged($loginId,$start,$direction,$limit);
                return $rows ;
            }

        }

        function getLatest($loginId,$limit) {
            $rows = mysql\Invoice::getLatest($loginId,$limit);
            return $rows ;
        }

        function  setOpBit($invoiceId,$bit) {
            mysql\Invoice::setOpBit($invoiceId,$bit);
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
