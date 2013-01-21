<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Order {

        function getOnId($orderId) {
            $row = mysql\Order::getonId($orderId);
            return $row ;
        }

        function add($invoiceRow,$formData) {
            $orderId = mysql\Order::add($invoiceRow,$formData);
            return $orderId;
        }

        function  setState($orderId,$bit) {
            mysql\Order::setState($orderId,$bit);
        }
                                  
    }
}

?>
