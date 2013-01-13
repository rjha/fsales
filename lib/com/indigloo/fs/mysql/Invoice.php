<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class Invoice {

        static function getOnId($invoiceId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $invoiceId = $mysqli->real_escape_string($invoiceId);
             
            $sql = " select * from fs_invoice where id = %d ";
            $sql = sprintf($sql,$invoiceId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnId2($invoiceId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $invoiceId = $mysqli->real_escape_string($invoiceId);
             
             $sql = " select p.picture, p.link, p.message as post_text, inv.* ".
                " from fs_post p, fs_invoice inv ".
                " where inv.id = %d and inv.post_id = p.post_id " ;

            $sql = sprintf($sql,$invoiceId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getLatest($loginId,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
             
            settype($loginId, "integer");
            settype($limit, "integer");

            $sql = " select p.picture, p.link, p.message as post_text, inv.* ".
                " from fs_post p, fs_invoice inv ".
                " where inv.id = %d ".
                " and inv.post_id = p.post_id order by inv.id desc limit %d " ;
            
            $sql = sprintf($sql,$loginId,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPaged($loginId,$start,$direction,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($loginId, "integer");
            settype($limit, "integer");
            settype($start,"integer");
            $direction = $mysqli->real_escape_string($direction);

             $sql = 
                " select p.picture, p.link, p.message as post_text ,inv.* ".
                " from fs_post p, fs_invoice inv ".
                " where inv.id = %d ".
                " and inv.post_id = p.post_id  " ;

            $sql = sprintf($sql,$loginId);
            $q = new MySQL\Query($mysqli);
            $q->setPrefixAnd();
            $sql .= $q->getPagination($start,$direction,"inv.id",$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);

            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        static function setOpBit($invoiceId,$bit) {
            $dbh = NULL ;
            
            try {
                //input check
                settype($invoiceId, "integer");
                settype($bit, "integer");

                $dbh =  PDOWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                $sql = " update fs_invoice set op_bit = %d where id = %d " ;
                $sql = sprintf($sql,$bit,$invoiceId);
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

        static function create($loginId,
                                $commentId,
                                $name,
                                $email,
                                $unitPrice,
                                $quantity,
                                $seller_info) {

            $dbh = NULL ;
            
            try {

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                // @todo input check
                // get comment details
                $commentRow = Comment::getOnId($commentId);
                // @todo raise error on empty comment
                $sourceRow = Source::getOnId($commentRow["source_id"]);

                $sql1 = " insert into fs_invoice(login_id, source_id, source_name,post_id, comment_id, name, ".
                        " email, quantity, unit_price,total_price, op_bit,seller_info,created_on, updated_on) " .
                        " values (:login_id, :source_id, :source_name, :post_id, :comment_id, :name, ".
                        " :email, :quantity, :unit_price, :total_price, 1, :seller_info, now(), now()) ";

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":login_id", $loginId);
                $stmt1->bindParam(":source_id", $commentRow["source_id"]);
                $stmt1->bindParam(":source_name", $sourceRow["name"]);
                $stmt1->bindParam(":post_id", $commentRow["post_id"]);
                $stmt1->bindParam(":comment_id", $commentId);

                $stmt1->bindParam(":name", $name);
                $stmt1->bindParam(":email", $email);
                $stmt1->bindParam(":quantity", $quantity);

                $stmt1->bindParam(":unit_price", $unitPrice);
                $totalPrice = $unitPrice * $quantity ;
                $stmt1->bindParam(":total_price", $totalPrice);
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
