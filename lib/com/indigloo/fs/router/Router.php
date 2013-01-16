<?php
namespace com\indigloo\fs\router{


    class Router extends \com\indigloo\core\Router{

        function __construct() {

        }

        function __destruct() {

        }

        function initTable() {
            
            $this->createRule('^/$', 'com\indigloo\fs\controller\Home');
            $this->createRule('^ghost/canvas/$','com\indigloo\fs\controller\Home');
        }
    }
}
?>
