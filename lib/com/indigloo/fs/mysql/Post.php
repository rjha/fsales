<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class Post {


        static function getObjectId($postId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            
            //input check
            $postId = $mysqli->real_escape_string($postId);

            $sql = " select object_id from fs_post where post_id = '%s' ";
            $sql = sprintf($sql,$postId);

            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function add($sourceId,$postId,$fbPost) {
                
            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();

                //Tx start
                $dbh->beginTransaction();
                
            
                // @todo input check
                // source_id : maxlen 64
                // post_id : maxlen 64
                // all ts : maxlen 16

                $sql1 = " insert into fs_post(source_id,post_id, picture,link, object_id,message, ".
                        " created_on, updated_on )".
                        " values(:source_id, :post_id, :picture, :link, :object_id, :message, now(), now())" ;

                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":source_id", $sourceId);
                $stmt1->bindParam(":post_id", $postId);
                $stmt1->bindParam(":picture", $fbPost["picture"]);

                $stmt1->bindParam(":link", $fbPost["link"]);
                $stmt1->bindParam(":object_id", $fbPost["object_id"]);
                $stmt1->bindParam(":message", $fbPost["message"]);
                
                $stmt1->execute();
                $stmt1 = NULL ;
                
                //flip fs_stream.post.d_bit to 1 

                $sql2 = " update fs_stream set d_bit = 1 where post_id = :post_id " ;
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":post_id", $postId);
                $stmt2->execute();
                $stmt2 = NULL ;
                
                
                //Tx end
                $dbh->commit();
                $dbh = null;
                
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
