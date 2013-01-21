<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;
    use \com\indigloo\exception\UIException ;

    class Order {

        function getOnId($orderId) {
            $row = mysql\Order::getonId($orderId);
            return $row ;
        }

        // Rules
        // Rule number zero : never trust user input!
        // first name/ last name - min :3 max 30 | alphanumeric
        // first name <> last name
        // email : 64/ valid format
        // phone : digits only / max 16
        // address : 100 / min 6 
        // city : 3-30 chars
        // state : required
        // pincode 2-12 : numbers only

        function validateForm($fvalues) {
            // valid email check
            if(!Util::contains($fvalues["email"], '@')) {
                $message = "email is not in valid format" ;
                throw new UIException(array($message));
            }

            // phone digits check
            if(!ctype_digit($fvalues["phone"]) 
                || !ctype_digit($fvalues["ship_phone"])) {
                $message = "only numbers are allowed in phone " ;
                throw new UIException(array($message));
            }

            // pincode numbers check
            if(!ctype_digit($fvalues["billing_pincode"]) 
                || !ctype_digit($fvalues["ship_pincode"])) {
                $message = "only numbers are allowed in a Pincode(billing)" ;
                throw new UIException(array($message));
            }

            //first name /last name can be alphanumeric only
            if(!ctype_alnum($fvalues["first_name"]) 
                || !ctype_alnum($fvalues["last_name"])
                || !ctype_alnum($fvalues["ship_first_name"])
                || !ctype_alnum($fvalues["ship_last_name"])) {

                $message = "Name can be composed of letters and numbers only." ;
                throw new UIException(array($message));
            }

            // first name <> last name check
            if(strcmp($fvalues["first_name"],$fvalues["last_name"]) == 0) {
                $message = "first name and last name cannot be same." ;
                throw new UIException(array($message));
            }
            
            if(strcmp($fvalues["ship_first_name"],$fvalues["ship_last_name"]) == 0) {
                $message = "first name and last name (shipping) cannot be same." ;
                throw new UIException(array($message));
            }

        }

        function add($invoiceRow,$formData) {
            $orderId = mysql\Order::add($invoiceRow,$formData);
            return $orderId;
        }

        function update($orderId,$formData) {
            mysql\Order::update($orderId,$formData);
        }

        function  setState($orderId,$bit) {
            mysql\Order::setState($orderId,$bit);
        }
                                  
    }
}

?>
