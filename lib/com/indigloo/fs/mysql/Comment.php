<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class Comment {

        static function add($sourceId,$postId,$fbComments) {
                
            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();

              
                // @todo input check
                // source_id : maxlen 64
                // post_id : maxlen 64
                // from_id : maxlen 64
                // user_name : maxlen 64
                // all ts : maxlen 16

                $sql1 = " insert into fs_comment(source_id,post_id,from_id, user_name,message, ".
                        " created_on, updated_on )".
                        " values(:source_id, :post_id, :from_id, :user_name, :message, now(), now())" ;

                $sql2 = " update fs_stream set last_stream_ts = :comment_ts where post_id = :post_id" ;

                foreach($fbComments as $fbComment) {
                    //Tx start
                    $dbh->beginTransaction();
                
            
                    $stmt1 = $dbh->prepare($sql1);
                    $stmt1->bindParam(":source_id", $sourceId);
                    $stmt1->bindParam(":post_id", $postId);

                    $stmt1->bindParam(":from_id", $fbComment["from_id"]);
                    $stmt1->bindParam(":user_name", $fbComment["user_name"]);
                    $stmt1->bindParam(":message", $fbComment["message"]);

                    $stmt1->execute();

                    $stmt2 = $dbh->prepare($sql2);

                    $stmt2->bindParam(":post_id", $postId);
                    $stmt2->bindParam(":comment_ts", $fbComment["created_time"]);

                    $stmt1 = NULL ;
                    $stmt2 = NULL ;
                    //Tx end
                    $dbh->commit();

                }
                
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
