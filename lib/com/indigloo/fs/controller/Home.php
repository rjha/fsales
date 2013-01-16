<?php
namespace com\indigloo\fs\controller{


    use \com\indigloo\Util as Util;
    use \com\indigloo\Url;
    use \com\indigloo\Configuration as Config ;

    
    class Home {

        
        function __construct() {
            
        }

        function process($params,$options) {
            $file = APP_WEB_DIR. '/app/home.php' ;
            include ($file);
        }
        
    }
}
?>
