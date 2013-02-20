<?php

namespace com\indigloo\fs\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;

    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;


    class Stream {
        
         
        static function get($limit) {

            //input check
            settype($limit, "integer");

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            
            // process old posts first
            $sql = " select * from fs_stream where op_bit = 1 order by id ASC LIMIT %d " ;
            $sql = sprintf($sql,$limit);

            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;

        }

        static function getSources() {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select * from fs_source " ;
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function add($sourceId,$stream_ts,$fbPosts) {

            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();

                //Tx start
                $dbh->beginTransaction();
                
                

                // @todo input check
                // source_id : maxlen 64
                // post_id : maxlen 64
                // all ts : maxlen 16

                $sql1 = " insert into fs_stream(source_id,post_id,last_stream_ts, ".
                        " next_stream_ts, version, created_on, updated_on )".
                        " values(:source_id, :post_id, :last_ts, :next_ts,1, now(), now())".
                        " ON DUPLICATE KEY update next_stream_ts = :next_ts, version = version +1 , " .
                        " updated_on = now() " ;
                
                $ts_array = array();
                $max_ts = $stream_ts ;

                foreach($fbPosts as $fbPost) {
                    $post_ts = (int) $fbPost["updated_time"];
                    array_push($ts_array,$post_ts);
                    // stream FQL pokes at last N posts
                    // so that means we can bring in stream.posts 
                    // before stream.last_ts as well

                    if($post_ts > $stream_ts){
                        $stmt1 = $dbh->prepare($sql1);
                        $stmt1->bindParam(":source_id", $sourceId);
                        $stmt1->bindParam(":post_id", $fbPost["post_id"]);
                        $stmt1->bindParam(":last_ts", $stream_ts);
                        $stmt1->bindParam(":next_ts", $fbPost["updated_time"]);
                        $stmt1->execute();
                        
                        if(Config::getInstance()->is_debug()) {
                            $message = "fs_stream insert post = %s , updated_on = %s " ;
                            $message = sprintf($message,$fbPost["post_id"], $fbPost["updated_time"]);
                            Logger::getInstance()->debug($message);
                        }
                    }
                }

                $stmt1 = NULL ;

                if(!empty($ts_array)) {
                    sort($ts_array, SORT_NUMERIC);
                    //max is now the last element
                    $index = sizeof($ts_array) - 1 ;
                    $max_ts = $ts_array[$index]; 
                }

                // update stream_ts for source
                $sql2 = " update fs_source set last_stream_ts = '%s' where source_id = '%s' ";
                $sql2 = sprintf($sql2,$max_ts,$sourceId);
                $dbh->exec($sql2);

                if(Config::getInstance()->is_debug()) {
                    $message = " fs_source :: update source = %s , new ts = %s " ;
                    $message = sprintf($message,$sourceId, $max_ts);
                    Logger::getInstance()->debug($message);
                }

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

        static function addSources($loginId,$pages) { 
                   
             $dbh = NULL ;
             
             try {

                $dbh =  PDOWrapper::getHandle();
                //Tx:start
                $dbh->beginTransaction();

                $sql1 = " delete from fs_source where login_id  = :login_id " ;
                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":login_id", $loginId);
                $stmt1->execute();
                
                foreach($pages as $page) {
                    //@todo check : source_id : maxlength :64
                    $sql2 = " insert into fs_source(login_id,source_id,name,token,last_stream_ts, ".
                            " created_on, updated_on) " .
                            " values(:login_id, :id, :name, :token, unix_timestamp(now()-INTERVAL 1 DAY), ".
                             " now(), now())" ;

                    $stmt2 = $dbh->prepare($sql2);
                    $stmt2->bindParam(":login_id", $loginId);
                    $stmt2->bindParam(":id", $page["id"]);
                    $stmt2->bindParam(":name", $page["name"]);
                    $stmt2->bindParam(":token", $page["access_token"]);

                    $stmt2->execute();
                }

                $sql3 = " update fs_facebook_user set op_bit = 2 where login_id = %d " ;
                $sql3 = sprintf($sql3,$loginId) ;
                $dbh->exec($sql3);


                //Tx:end
                $dbh->commit();
                $dbh = null;
                $stmt1 = NULL ;
                $stmt2 = NULL ;
                
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

        static function setState($sourceId,$postId,$bit) {
            $dbh = NULL ;
            
            try {
                
                settype($bit, "integer");

                $dbh =  PDOWrapper::getHandle();
                
                //Tx start
                $dbh->beginTransaction();
                
                $sql = " update fs_stream set op_bit = %d where post_id = '%s' and source_id = '%s' " ;
                $sql = sprintf($sql,$bit,$postId,$sourceId);
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

    }
}

?>
