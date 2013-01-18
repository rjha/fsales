<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    
    use \com\indigloo\Logger ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    use \com\indigloo\fs\Constants as AppConstants ;

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

        static function getLatest($sourceId,$ft,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sourceId = $mysqli->real_escape_string($sourceId);
            settype($limit, "integer");

            $ftPredicate = (strcmp($ft, "verb") == 0) ? " and c.verb = 1 " : "" ;

            $sql = " select p.picture, p.link, p.message as post_text, c.* ".
                " from fs_post p, fs_comment c ".
                " where c.source_id = '%s'  %s ".
                " and c.post_id = p.post_id order by created_ts desc limit %d " ;
            
            $sql = sprintf($sql,$sourceId,$ftPredicate,$limit);
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getPaged($sourceId,$ft,$start,$direction,$limit) {

            $mysqli = MySQL\Connection::getInstance()->getHandle();

            //sanitize input
            settype($limit, "integer");
            settype($start,"integer");

            $sourceId = $mysqli->real_escape_string($sourceId);
            $direction = $mysqli->real_escape_string($direction);

            $ftPredicate = (strcmp($ft, "verb") == 0) ? " and c.verb = 1 " : "" ;

            $sql = 
                " select p.picture, p.link, p.message as post_text ,c.* ".
                " from fs_post p, fs_comment c ".
                " where c.source_id = '%s' ".
                " and c.post_id = p.post_id  %s " ;

            $sql = sprintf($sql,$sourceId,$ftPredicate);
            $q = new MySQL\Query($mysqli);
            $q->setPrefixAnd();
            $sql .= $q->getPagination($start,$direction,"c.created_ts",$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            
            //reverse rows for 'before' direction
            if($direction == 'before') {
                $results = array_reverse($rows) ;
                return $results ;
            }

            return $rows;
        }

        static function add($sourceId,$postId,$last_ts,$version,$limit,$fbComments) {
                
            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();

                //Tx1: start
                $dbh->beginTransaction();
                
                // @todo input check
                // source_id : maxlen 64
                // post_id : maxlen 64
                // from_id : maxlen 64
                // user_name : maxlen 64
                // all ts : maxlen 16

                $sql1 = " insert into fs_comment(source_id,post_id, comment_id,from_id, " .
                        " user_name, message, created_ts, created_on, updated_on, verb) ".
                        " values(:source_id, :post_id, :comment_id, :from_id, ".
                        " :user_name, :message, :created_ts, now(), now(), :verb) " .
                        " on duplicate key update dup_count = dup_count + 1 " ;
                 
                $max_ts = (int) $last_ts ;
               
                foreach($fbComments as $fbComment) {
            
                    $stmt1 = $dbh->prepare($sql1);

                    $stmt1->bindParam(":source_id", $sourceId);
                    $stmt1->bindParam(":post_id", $postId);
                    $stmt1->bindParam(":comment_id", $fbComment["comment_id"]);

                    $stmt1->bindParam(":from_id", $fbComment["from_id"]);
                    $stmt1->bindParam(":user_name", $fbComment["user_name"]);

                    // comment contains verb?
                    $comment_text =  $fbComment["message"];
                    $verb = (Util::contains($comment_text, AppConstants::COMMENT_VERB)) ? 1 : 0 ;

                    $stmt1->bindParam(":message",$comment_text);
                    $stmt1->bindParam(":verb",$verb);
                    

                    $stmt1->bindParam(":created_ts", $fbComment["created_time"]);
                    $stmt1->execute();


                    if(Config::getInstance()->is_debug()) {
                        $message = "fs_comment insert id = %s , created_on = %s " ;
                        $message = sprintf($message,$fbComment["comment_id"], $fbComment["created_time"]);
                        Logger::getInstance()->debug($message);
                    }

                    $comment_ts = (int) $fbComment["created_time"];
                    $max_ts = ($comment_ts > $max_ts) ? $comment_ts : $max_ts ;
                }
                
                $stmt1 = NULL ;

                $sql2 = " update fs_stream set last_stream_ts = '%s' ".
                        " where post_id = '%s' and source_id = '%s' " ;
                
                $sql2 = sprintf($sql2,$max_ts,$postId,$sourceId);
                $dbh->exec($sql2);

                //Tx1 end
                $dbh->commit();

                $num_comments = sizeof($fbComments);
                if($num_comments < $limit) {
                    // 
                    // we got less than limit # of comments
                    // and this post has not been updated in the interim
                    // That means we have run through all the comments for this
                    // post in stream
                    // comparison is with version column to implement optimistic locking
                    // 
                    //Tx2 :start
                    $dbh->beginTransaction();
                    
                    $sql3 = " delete from fs_stream where post_id = '%s' and version = %d " ;
                    $sql3 = sprintf($sql3,$postId,$version);
                    $dbh->exec($sql3);
                    //Tx2:end
                    $dbh->commit();

                    if(Config::getInstance()->is_debug()) {
                        $message = "fs_stream :: try deleting : post_id = %s , version = %d " ;
                        $message = sprintf($message,$postId, $version);
                        Logger::getInstance()->debug($message);
                    }
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
