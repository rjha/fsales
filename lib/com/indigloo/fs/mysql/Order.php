<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class Order {

        static function getOnId($orderId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
             
            settype($orderId, "integer") ;
            $sql = " select * from fs_order where id = %d ";
            $sql = sprintf($sql,$orderId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function add($invoiceRow,$formData) {
            
            $dbh = NULL ;
            
            try {

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $sql1 = 
                    " insert into fs_order (invoice_id, first_name, last_name, email,phone, ".
                    " total_price, currency, item_description, ip_address, billing_address, ".
                    " billing_city, billing_state, billing_pincode, billing_country, ".
                    " shipping_first_name, shipping_last_name, shipping_address, shipping_city, ".
                    " shipping_state, shipping_pincode, shipping_country, tx_date, created_on,op_bit) ".
                    " values(:invoice_id, :first_name, :last_name, :email, :phone,".
                    " :amount, :currency, :item_desc, :ip, :address, ".
                    " :city, :state, :pin, :country, :s_first_name, :s_last_name, :s_address,:s_city, ".
                    " :s_state, :s_pin, :s_country, now(), now(),1) " ;

                
                $currency =  "INR" ;
                $country = "India";
                $ipAddress =  Url::getRemoteIp();

                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":invoice_id", $invoiceRow["id"]);
                $stmt1->bindParam(":amount", $invoiceRow["total_price"]);
                $stmt1->bindParam(":item_desc", $invoiceRow["post_text"]);

                $stmt1->bindParam(":first_name", $formData["first_name"]);
                $stmt1->bindParam(":last_name", $formData["last_name"]);
                $stmt1->bindParam(":email", $formData["email"]);
                $stmt1->bindParam(":phone", $formData["phone"]);
               
                $stmt1->bindParam(":currency", $currency);
                $stmt1->bindParam(":ip", $ipAddress);

                $stmt1->bindParam(":address", $formData["billing_address"]);
                $stmt1->bindParam(":city", $formData["billing_city"]);
                $stmt1->bindParam(":state", $formData["billing_state"]);
                $stmt1->bindParam(":pin", $formData["billing_pincode"]);
                $stmt1->bindParam(":country", $country);
                
                // @todo: shipping address can be different from billing address
                $stmt1->bindParam(":s_first_name", $formData["first_name"]);
                $stmt1->bindParam(":s_last_name", $formData["last_name"]);
                $stmt1->bindParam(":s_address", $formData["billing_address"]);
                $stmt1->bindParam(":s_city", $formData["billing_city"]);
                $stmt1->bindParam(":s_state", $formData["billing_state"]);
                $stmt1->bindParam(":s_pin", $formData["billing_pincode"]);
                $stmt1->bindParam(":s_country", $country);
                
                $stmt1->execute();
                $stmt1 = NULL ;

                $orderId = $dbh->lastInsertId();
                settype($orderId, "integer");
                //Tx end
                $dbh->commit();
                $dbh = null;

                return $orderId ;
                
            }catch (\PDOException $e) {
                $dbh->rollBack(); 
                $dbh = null;
                throw new DBException($e->getMessage(),$e->getCode());

            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }
              
        }
    }
}

?>
