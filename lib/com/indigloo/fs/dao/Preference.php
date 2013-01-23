<?php

namespace com\indigloo\fs\dao {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Preference {

    	function set($key,$value) {
    		mysql\Preference::set($key,$value);
    	}

    	function get($key,$default=NULL) {
    		$value = NULL ;
    		$row = mysql\Preference::get($key);
    		if(empty($row) && !empty($default)) {
    			return $default ;
    		}

    		$value = $row["value"] ;
    		return $value ;

    	}
    	

    }

}

?>
