<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class Comment {

        static function getOnId($commentId) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $commentId = $mysqli->real_escape_string($commentId);
             
            $sql = " select p.picture, p.link, p.message as post_text, c.* " .
                " from fs_post p, fs_comment c where c.comment_id = '%s' ".
                " and c.post_id = p.post_id " ;

            $sql = sprintf($sql,$commentId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);

            return $row;
        }

        static function getLatest($sourceId,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sourceId = $mysqli->real_escape_string($sourceId);
            settype($limit, "integer");

            $sql = " select p.picture, p.link, p.message as post_text, c.* ".
                " from fs_post p, fs_comment c ".
                " where c.source_id = '%s' ".
                " and c.post_id = p.post_id order by created_ts desc limit %d " ;
            
            $sql = sprintf($sql,$sourceId,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPaged($sourceId,$start,$direction,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit, "integer");
            settype($start,"integer");

            $sourceId = $mysqli->real_escape_string($sourceId);
            $direction = $mysqli->real_escape_string($direction);

             $sql = 
                " select p.picture, p.link, p.message as post_text ,c.* ".
                " from fs_post p, fs_comment c ".
                " where c.source_id = '%s' ".
                " and c.post_id = p.post_id  " ;

            $sql = sprintf($sql,$sourceId);
            $q = new MySQL\Query($mysqli);
            $q->setPrefixAnd();
            $sql .= $q->getPagination($start,$direction,"c.created_ts",$limit);

            //@debug 
            // echo $sql; exit ;

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);


            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

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
