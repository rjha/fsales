<?php

namespace com\indigloo\fs\mysql {

    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Util as Util ;
    use \com\indigloo\Configuration as Config ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;


    class Account {
        
        static function addPages($loginId,$pages) { 
                   
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
                
                    $sql2 = " insert into fs_source(login_id,source_id,name,token,last_update_ts, created_on, updated_on) " ;
                    $sql2 .= " values(:login_id, :id, :name, :token, unix_timestamp(now()), now(), now())" ;

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
