<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Url as Url ;
    use \com\indigloo\mysql as MySQL;

    use \com\indigloo\Logger ;
    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    use \com\indigloo\fs\Constants as AppConstants;

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
                    " shipping_first_name, shipping_last_name, shipping_phone, shipping_address, shipping_city, ".
                    " shipping_state, shipping_pincode, shipping_country, tx_date, created_on,op_bit) ".
                    " values(:invoice_id, :first_name, :last_name, :email, :phone,".
                    " :amount, :currency, :item_desc, :ip, :address, ".
                    " :city, :state, :pin, :country, :s_first_name, :s_last_name, :s_phone,:s_address,:s_city, ".
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
                
                // shipping address can be different from billing address
                $stmt1->bindParam(":s_first_name", $formData["ship_first_name"]);
                $stmt1->bindParam(":s_last_name", $formData["ship_last_name"]);
                $stmt1->bindParam(":s_phone", $formData["ship_phone"]);
               
                $stmt1->bindParam(":s_address", $formData["ship_address"]);
                $stmt1->bindParam(":s_city", $formData["ship_city"]);
                $stmt1->bindParam(":s_state", $formData["ship_state"]);
                $stmt1->bindParam(":s_pin", $formData["ship_pincode"]);
                $stmt1->bindParam(":s_country", $country);
                
                $stmt1->execute();
                $stmt1 = NULL ;

                $orderId = $dbh->lastInsertId();
                settype($orderId, "integer");

                // update fs_invoice.op_bit to processing state (3)
                // processing state means : user tried to create an order
                // whether that order went through or not - we do not know.
                // update fs_invoice.p_order_id so when we try to access this invoice
                // we can be redirected to order instead.

                $sql2 = " update fs_invoice set op_bit = %d , p_order_id = %d where id = %d ";
                $sql2 = sprintf($sql2,AppConstants::INVOICE_PROCESSING_STATE,$orderId,$invoiceRow["id"]);
                $dbh->exec($sql2);

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

        static function setState($orderId,$bit) {
            $dbh = NULL ;
            
            try {
                //input check
                settype($orderId, "integer");
                settype($bit, "integer");

                $dbh =  PDOWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                // @imp state can be changed in one direction only
                // 1->2->3 always increasing.
                $sql = " update fs_order set op_bit = %d where id = %d and op_bit <= %d " ;
                $sql = sprintf($sql,$bit,$orderId,$bit);
                $dbh->exec($sql);

                //Tx end
                $dbh->commit();
                $dbh = null;


            } catch(\Exception $ex) {
                $dbh->rollBack();
                $dbh = null;
                $message = $ex->getMessage();
                throw new DBException($message);
            }

        }

        static function update($orderId,$formData) {
            
            $dbh = NULL ;
            
            try {

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $sql1 = 
                " update fs_order set first_name = :first_name,last_name = :last_name, ".
                " email = :email, phone = :phone, ip_address = :ip, billing_address = :b_address, ".
                " billing_city = :b_city, billing_state = :b_state, billing_pincode = :b_pin, ".
                " shipping_first_name = :s_first_name, shipping_last_name = :s_last_name, ".
                " shipping_phone = :s_phone, shipping_address = :s_address, shipping_city = :s_city, ".
                " shipping_state = :s_state, shipping_pincode = :s_pin, tx_date = now(), ".
                " updated_on = now() where id = :order_id " ;
                
                $ipAddress =  Url::getRemoteIp();

                $stmt1 = $dbh->prepare($sql1);

                $stmt1->bindParam(":order_id", $orderId);
                
                $stmt1->bindParam(":first_name", $formData["first_name"]);
                $stmt1->bindParam(":last_name", $formData["last_name"]);
                $stmt1->bindParam(":email", $formData["email"]);
                $stmt1->bindParam(":phone", $formData["phone"]);
                
                $stmt1->bindParam(":ip", $ipAddress);

                $stmt1->bindParam(":b_address", $formData["billing_address"]);
                $stmt1->bindParam(":b_city", $formData["billing_city"]);
                $stmt1->bindParam(":b_state", $formData["billing_state"]);
                $stmt1->bindParam(":b_pin", $formData["billing_pincode"]);
                
                // shipping address can be different from billing address
                $stmt1->bindParam(":s_first_name", $formData["ship_first_name"]);
                $stmt1->bindParam(":s_last_name", $formData["ship_last_name"]);
                $stmt1->bindParam(":s_phone", $formData["ship_phone"]);
               
                $stmt1->bindParam(":s_address", $formData["ship_address"]);
                $stmt1->bindParam(":s_city", $formData["ship_city"]);
                $stmt1->bindParam(":s_state", $formData["ship_state"]);
                $stmt1->bindParam(":s_pin", $formData["ship_pincode"]);
                
                $stmt1->execute();
                $stmt1 = NULL ;

                //Tx end
                $dbh->commit();
                $dbh = null;
                
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
