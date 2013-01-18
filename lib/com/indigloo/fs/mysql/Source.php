<?php
namespace com\indigloo\fs\mysql {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\mysql as MySQL;
    use \com\indigloo\Logger ;

    use \com\indigloo\mysql\PDOWrapper;
    use \com\indigloo\exception\DBException as DBException;

    class Source {

        static function getToken($sourceId) {
            
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($sourceId, "integer");
            
            $sql = "select token from fs_source where source_id = '%s' " ;
            $sql = sprintf($sql,$sourceId);
            
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;

        }

        static function getAll($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");
            
            $sql = "select * from fs_source where login_id = %d " ;
            $sql = sprintf($sql,$loginId);
            
            $rows = MySQL\Helper::fetchRows($mysqli, $sql);
            return $rows;
        }

        static function getDefault($loginId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            settype($loginId, "integer");
            
            $sql = "select * from fs_source where login_id = %d and is_default = 1 " ;
            $sql = sprintf($sql,$loginId);
            
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function getOnId($sourceId) {
            $mysqli = MySQL\Connection::getInstance()->getHandle();
            
            $sourceId = $mysqli->real_escape_string($sourceId) ;
            $sql = "select * from fs_source where source_id = '%s' " ;
            $sql = sprintf($sql,$sourceId);
            
            $row = MySQL\Helper::fetchRow($mysqli, $sql);
            return $row;
        }

        static function makeDefault($loginId, $sourceId) {
            $dbh = NULL ;
             
            try {

                $dbh =  PDOWrapper::getHandle();
              
                // first set everyone to 0 
                $sql1 = " update fs_source set is_default = 0 " ;
                $sql2 = "update fs_source set is_default = 1 where login_id = %d and source_id = '%s' ";
                $sql2 = sprintf($sql2,$loginId,$sourceId);

                //Tx start
                $dbh->beginTransaction();
                $dbh->exec($sql1);
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
