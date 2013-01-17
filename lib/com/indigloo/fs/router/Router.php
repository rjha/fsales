<?php
namespace com\indigloo\fs\router{


    class Router extends \com\indigloo\core\Router{

        function __construct() {

        }

        function __destruct() {

        }

        function initTable() {
            
            $this->createRule('^/$', 'com\indigloo\fs\controller\Home');
            // facebook canvas URL
            // we need to map the URL that we distribute or redirect
            // from our app. once the content is loaded into Facbook canvas
            // it is business as usual.
            // e.g.
            // ghost/canvas/
            // ghost/canvas/login
            // ghost/canvas/dashboard
            // ghost/canvas/select-page
            // ghost/canvas/invoice/1234
            // ghost/canvas/order/1234

            $this->createRule('^ghost/canvas/(.*)$','com\indigloo\fs\controller\Canvas');

        }
    }
}
?>
