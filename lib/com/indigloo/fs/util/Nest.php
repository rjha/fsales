<?php

namespace com\indigloo\fs\util {

    use \com\indigloo\Util as Util ;
    use \com\indigloo\Logger as Logger ;
    use \com\indigloo\fs\mysql as mysql;

    class Nest {

    	static function comment_filter() {
    		$key = "fs:user:comment:filter" ;
    		return $key ;
    	}
    	
    	static function source_filter() {
    		$key = "fs:user:source:filter" ;
    		return $key ;
    	}

    }

}

?>
