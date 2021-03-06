<?php

    $s_time = microtime(true);
    require_once('fs-app.inc');
    require_once(APP_WEB_DIR . '/app/inc/header.inc');

    use \com\indigloo\Configuration as Config ;
    use \com\indigloo\Logger  as Logger ;
    

    $router = new com\indigloo\fs\router\Router();
    $originalURI = $_SERVER['REQUEST_URI'];
    $requestURI = $originalURI ;

    //initialize routing table
    $router->initTable();
    $pos = strpos($originalURI, '?');
    $qpart = NULL ;

    if($pos !== false) {
        // remove the part after ? from Url
        // routing does not depends on query parameters
        $requestURI = substr($originalURI,0,$pos);
        $qpart = substr($originalURI, $pos+1);
    }

    $route = $router->getRoute($requestURI);

    if(is_null($route)) {
        //No valid route for this path
        $message = sprintf("No route for path %s",$requestURI);
        Logger::getInstance()->error($message);

        $controller = new \com\indigloo\fs\controller\Http404();
        $controller->process();
        exit;

    } else {
        $controllerName = $route["action"];
        //add path and query 
        $options = array();
        $options["path"] = $requestURI ;
        $options["query"] = $qpart;
        $route["options"] = $options ;

        if(Config::getInstance()->is_debug()) {
            $message = sprintf("controller %s :: path is %s  ", $controllerName, $requestURI);
            Logger::getInstance()->debug($message);
            Logger::getInstance()->dump($route);
        }

        $controller = new $controllerName();
        $controller->process($route["params"], $route["options"]);

    }

    $e_time = microtime(true);
    printf(" \n <!-- Request %s took %f milliseconds --> \n", $originalURI, ($e_time - $s_time)*1000);

?>
