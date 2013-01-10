<?php

namespace com\indigloo\fs\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;


    class Stream {
        
        static function getLastTS($postId) {
            //@todo input check for post_id string
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            // @todo : how do we limit the # of posts here?
            $sql = " select   last_stream_ts  from  fs_stream where post_id = '%s' " ;
            $sql = sprintf($sql,$postId);
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }
        
        static function getPosts($limit) {

            //input check
            settype($limit, "integer");

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            // @todo : how do we limit the # of posts here?
            $sql = " select * from fs_stream " .
                    " order by updated_on asc LIMIT %d" ;

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

        static function addPhotos($sourceId,$ts,$photos) {

            
            // new photo in stream for this source
            // Add with post.last_stream_ts = crawling_time
            // post.next_stream_ts = max_ts in this crawl
            // for an existing stream.post
            // update next_stream_ts
            // After adding all the photos in this stream
            // update source.last_stream_ts
             
            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();

                //Tx start
                $dbh->beginTransaction();
                
                

                // @todo input check
                // source_id : maxlen 64
                // post_id : maxlen 64
                // all ts : maxlen 16

                $sql1 = " insert into fs_stream(source_id,post_id,d_bit,last_stream_ts, ".
                        " next_stream_ts, created_on, updated_on )".
                        " values(:source_id, :post_id, 0, :source_ts, :post_ts, now(), now())".
                        " ON DUPLICATE KEY update next_stream_ts = :post_ts, updated_on = now() " ;
                
                $max_ts = (int) $ts ;
                
                foreach($photos as $photo) {
                    $stmt1 = $dbh->prepare($sql1);
                    $stmt1->bindParam(":source_id", $sourceId);
                    $stmt1->bindParam(":post_id", $photo["post_id"]);
                    $stmt1->bindParam(":source_ts", $ts);
                    $stmt1->bindParam(":post_ts", $photo["updated_time"]);
                    $stmt1->execute();
                    
                    $photo_ts = (int) $photo["updated_time"];
                    $max_ts = ($photo_ts > $max_ts) ? $photo_ts : $max_ts ;
                    
                }

                $stmt1 = NULL ;

                // update stream_ts for source
                $sql2 = " update fs_source set last_stream_ts = '%s' where source_id = '%s' ";
                $sql2 = sprintf($sql2,$max_ts,$sourceId);
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

        static function addSources($loginId,$pages) { 
                   
             $dbh = NULL ;
             
             try {

                $dbh =  PDOWrapper::getHandle();
                //Tx1:start
                $dbh->beginTransaction();

                $sql1 = " delete from fs_source where login_id  = :login_id " ;
                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":login_id", $loginId);
                $stmt1->execute();
                
                 //Tx1:end
                $dbh->commit();
                //Tx2:start
                $dbh->beginTransaction();

                foreach($pages as $page) {
                    //@todo check : source_id : maxlength :64
                    $sql2 = " insert into fs_source(login_id,source_id,name,token,last_stream_ts, created_on, updated_on) " ;
                    $sql2 .= " values(:login_id, :id, :name, :token, unix_timestamp(now() - INTERVAL 1 DAY), now(), now())" ;

                    $stmt2 = $dbh->prepare($sql2);
                    $stmt2->bindParam(":login_id", $loginId);
                    $stmt2->bindParam(":id", $page["id"]);
                    $stmt2->bindParam(":name", $page["name"]);
                    $stmt2->bindParam(":token", $page["access_token"]);

                    $stmt2->execute();
                }

                 
                //Tx2:end
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

    }
}

?>
