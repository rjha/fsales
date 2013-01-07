<?php

namespace com\indigloo\fs\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;


    class Stream {
        
        static function getSources() {

            $mysqli = MySQL\Connection::getInstance()->getHandle();
            $sql = " select * from fs_source " ;
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function addPhotos($sourceId,$ts,$photos) {

            // add new photo_id in stream + updated_time for further
            // monitoring
            // after success :- update last_stream_ts for this source.
             
            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();

                //Tx start
                $dbh->beginTransaction();
                
                $max_ts = (int) $ts ;

                // @todo input check
                // source_id : maxlen 64
                // post_id : maxlen 64
                // all ts : maxlen 16

                $sql1 = " insert into fs_stream(source_id,post_id,c_bit,p_bit,stream_ts,post_ts, comment_ts)" ;
                $sql1 .= " values(:source_id, :post_id, :zero, :zero, :stream_ts, :post_ts, :stream_ts )" ;
                
                foreach($photos as $photo) {
                    $stmt1 = $dbh->prepare($sql1);
                    $stmt1->bindParam(":zero", $zero);
                    $stmt1->bindParam(":source_id", $sourceId);
                    $stmt1->bindParam(":post_id", $photo["post_id"]);
                    $stmt1->bindParam(":stream_ts", $ts);
                    $stmt1->bindParam(":post_ts", $photo["updated_time"]);
                    $stmt1->execute();
                    
                    $photo_ts = (int) $photo["updated_time"];
                    $max_ts = ($photo_ts > $max_ts) ? $photo_ts : $max_ts ;
                    
                }

                $stmt1 = NULL ;

                // update stream_ts for source
                $sql2 = " update fs_source set last_stream_ts = :stream_ts where source_id = :source_id ";
                $stmt2 = $dbh->prepare($sql2);
                $stmt2->bindParam(":stream_ts", $max_ts);
                $stmt2->bindParam(":source_id", $sourceId);
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

        static function addSources($loginId,$pages) { 
                   
             $dbh = NULL ;
             
             try {

                $dbh =  PDOWrapper::getHandle();
                //Tx start
                $dbh->beginTransaction();

                $sql1 = " delete from fs_source where login_id  = :login_id " ;
                $stmt1 = $dbh->prepare($sql1);
                $stmt1->bindParam(":login_id", $loginId);
                $stmt1->execute();
                $stmt1 = NULL ;

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
