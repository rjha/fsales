#!/usr/bin/php
<?php


    error_reporting(-1);

    function make_js_bundle($root3p,$rootApp) {
        //list of 3p files to concatenate
        $files = array();
        $files[] = "jquery/jquery-1.8.2.js" ;
        $files[] = "jquery/jquery.validate.1.10.0.js" ;
        $files[] = "bootstrap/2.1.1/js/bootstrap.js" ;

        $appFiles = array();
        $appFiles[] = "js/fs.js" ;
        
        //output file name
        $bundle = "fs-bundle.js" ;
        $fp = fopen($bundle,"w");


        for($i = 0 ; $i < sizeof($files) ;  $i++ ) {
            $glob = file_get_contents($root3p.$files[$i]);
            fwrite($fp,$glob);
            $separator = sprintf("\n\n /* cat:3p:file:%d:%s */ \n\n",$i+1,$files[$i]);
            fwrite($fp,$separator);
        }

        for($i = 0 ; $i < sizeof($appFiles) ;  $i++ ) {
            $glob = file_get_contents($rootApp.$appFiles[$i]);
            fwrite($fp,$glob);
            $separator = sprintf("\n\n /* cat:sc:file:%d:%s */ \n\n",$i+1,$appFiles[$i]);
            fwrite($fp,$separator);
        }

        fclose($fp);

    }

    function make_css_bundle($root3p,$rootApp) {
        //list of 3p files to concatenate
        $files = array();
        $files[] = "bootstrap/2.1.1/css/bootstrap.css" ;
       
        $appFiles = array();
        $appFiles[] = "css/fs.css" ;

        //output file name
        $bundle = "fs-bundle.css" ;
        $fp = fopen($bundle,"w");


        for($i = 0 ; $i < sizeof($files) ;  $i++ ) {
            $glob = file_get_contents($root3p.$files[$i]);
            fwrite($fp,$glob);
            $separator = sprintf("\n\n /* cat:3p:file:%d:%s */ \n\n",$i+1,$files[$i]);
            fwrite($fp,$separator);
        }

        for($i = 0 ; $i < sizeof($appFiles) ;  $i++ ) {
            $glob = file_get_contents($rootApp.$appFiles[$i]);
            fwrite($fp,$glob);
            $separator = sprintf("\n\n /* cat:sc:file:%d:%s */ \n\n",$i+1,$appFiles[$i]);
            fwrite($fp,$separator);
        }

        //reponsive css is last include
        $glob = file_get_contents($root3p.'bootstrap/2.1.1/css/bootstrap-responsive.css');
        fwrite($fp,$glob);

        fclose($fp);

    }

    //root of 3rd party libraries
    $root3p = "/home/rjha/code/github/webgloo/web/3p/" ;
    $rootApp = "/home/rjha/code/github/fsales/web/" ;

    make_js_bundle($root3p,$rootApp);
    make_css_bundle($root3p,$rootApp);



?>
