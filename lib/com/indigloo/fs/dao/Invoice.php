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
        
        function getOrderOnId($invoiceId) {
            $row = mysql\Invoice::getOrderOnId($invoiceId);
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

        function  setState($invoiceId,$bit) {
            mysql\Invoice::setState($invoiceId,$bit);
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

        function update($loginId,
                        $invoiceId,
                        $name,
                        $email,
                        $unitPrice,
                        $quantity,
                        $seller_info) {

            mysql\Invoice::update($loginId,
                                $invoiceId,
                                $name,
                                $email,
                                $unitPrice,
                                $quantity,
                                $seller_info);
        }
        
        function addCourierInfo($invoiceId,$courierInfo,$courierLink) {
            // Add courier info to fs_order
            // change fs_invoice.op_bit
            mysql\Invoice::addCourierInfo($invoiceId,$courierInfo,$courierLink);

        }                     
    }
}

?>
