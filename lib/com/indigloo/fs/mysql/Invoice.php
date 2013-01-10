<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;


    class Invoice {


        static function create($loginId,
                                $commentId,
                                $name,
                                $email,
                                $price,
                                $quantity,
                                $seller_info) {

            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                // @todo input check
                // get comment details
                $commentRow = \com\indigloo\fs\mysql\Comment::getOnId($commentId);
                // @todo raise error on empty comment

                $sql1 = " insert into fs_invoice(login_id, source_id, post_id, comment_id, name, ".
                        " email, quantity, total_price, op_bit,seller_info,created_on, updated_on) " .
                        " values (:login_id, :source_id, :post_id, :comment_id, :name, ".
                        " :email, :quantity, :price, 1, :seller_info, now(), now()) ";

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":login_id", $loginId);
                $stmt1->bindParam(":source_id", $commentRow["source_id"]);
                $stmt1->bindParam(":post_id", $commentRow["post_id"]);
                $stmt1->bindParam(":comment_id", $commentId);
                $stmt1->bindParam(":name", $name);
                $stmt1->bindParam(":email", $email);
                $stmt1->bindParam(":quantity", $quantity);
                $stmt1->bindParam(":price", $price);
                $stmt1->bindParam(":seller_info", $seller_info);

                $stmt1->execute();
                $stmt1 = NULL ;

                $invoiceId = $dbh->lastInsertId();
                settype($invoiceId, "integer");
                //Tx end
                $dbh->commit();
                $dbh = null;

                return $invoiceId ;
                
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
