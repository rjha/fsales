<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class Comment {

        static function add($sourceId,$postId,$ts1,$fbComments) {
                
            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();

                //Tx start
                $dbh->beginTransaction();
                
                // @todo input check
                // source_id : maxlen 64
                // post_id : maxlen 64
                // from_id : maxlen 64
                // user_name : maxlen 64
                // all ts : maxlen 16

                $sql1 = " insert into fs_comment(source_id,post_id, comment_id,from_id, " .
                        " user_name, message, created_ts, created_on, updated_on ) ".
                        " values(:source_id, :post_id, :comment_id, :from_id, ".
                        " :user_name, :message, :created_ts, now(), now()) " .
                        " on duplicate key update dup_count = dup_count + 1 " ;

                 $max_ts = (int) $ts1 ;
               
                foreach($fbComments as $fbComment) {
            
                    $stmt1 = $dbh->prepare($sql1);

                    $stmt1->bindParam(":source_id", $sourceId);
                    $stmt1->bindParam(":post_id", $postId);
                    $stmt1->bindParam(":comment_id", $fbComment["comment_id"]);

                    $stmt1->bindParam(":from_id", $fbComment["from_id"]);
                    $stmt1->bindParam(":user_name", $fbComment["user_name"]);
                    $stmt1->bindParam(":message", $fbComment["message"]);
                    $stmt1->bindParam(":created_ts", $fbComment["created_time"]);


                    $stmt1->execute();

                    $comment_ts = (int) $fbComment["created_time"];
                    $max_ts = ($comment_ts > $max_ts) ? $comment_ts : $max_ts ;
                }
                
                $stmt1 = NULL ;

                $sql2 = " update fs_stream set last_stream_ts = '%s' ".
                        " where post_id = '%s' and source_id = '%s' " ;
                        
                $sql2 = sprintf($sql2,$max_ts,$postId,$sourceId);
                $dbh->exec($sql2);

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
