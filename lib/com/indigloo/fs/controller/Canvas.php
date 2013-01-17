<?php
namespace com\indigloo\fs\controller{

     
    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Constants as Constants;
    
    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\html\Application as AppHtml ;
    use \com\indigloo\fs\api\Graph as GraphAPI ;


    class Canvas {

        
        function __construct() {
            
        }

        function process($params,$options) {

            //insert global ui mode into session
            $gWeb = \com\indigloo\core\Web::getInstance();
            $gWeb->store("global.ui.mode","canvas");
            
            $path = $options["path"];
            if($path == "/ghost/canvas/") {
                $this->process_home();
            } else if($path == "/ghost/canvas/dashboard") {
                $this->process_dashboard();
            }else if($path == "/ghost/canvas/select-page") {
                $this->process_select_page();
            } else if($path == "/ghost/canvas/login") {
                $this->process_login();
            } else if($path == "/ghost/canvas/login-error") {
                $this->process_login_error();
            } else {
                $controller = new \com\indigloo\fs\controller\Http404();
                $controller->process();
                exit;
            }
           
            
        }

        private function process_home() {
            $view = APP_WEB_DIR. '/app/view/home.tmpl' ;
            include ($view);
        }

        private function process_dashboard() {

            $gWeb = \com\indigloo\core\Web::getInstance();
            $qparams = Url::getRequestQueryParams();
            $loginId = Login::getLoginIdInSession();

            $sourceDao = new \com\indigloo\fs\dao\Source();
            $sources = $sourceDao->getAll($loginId);
            $default_source_id = $sourceDao->getDefault($loginId) ;
            $sourceId = (isset($qparams["source_id"])) ? $qparams["source_id"] : $default_source_id;
             
            //nothing in query and default not set.
            if(!empty($sources) && empty($sourceId)) {
                $sourceId = $sources[0]["source_id"];
            }

            $sourceHtml = "" ;
            $commentHtml = "" ;

            //pagination variables
           
            $pageSize = 10 ;
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);

            $startId = NULL;
            $endId = NULL;
            $gNumRecords = 0 ;
            $pageBaseURI ="/ghost/canvas/dashboard" ;
            
             
            if(!empty($sourceId)) {
                $sourceRow = $sourceDao->getOnId($sourceId);
                $commentDao = new \com\indigloo\fs\dao\Comment();
                $commentRows = $commentDao->getPaged($sourceId,$paginator);

                //fix pagination variables
                $gNumRecords = sizeof($commentRows) ;
                if ($gNumRecords > 0) {
                    $startId = $commentRows[0]["created_ts"];
                    $endId = $commentRows[$gNumRecords - 1]["created_ts"];
                }

                $sourceHtml = AppHtml::getSource($sourceRow,$sources);
                foreach($commentRows as $commentRow) {
                    $commentHtml .= AppHtml::getComment($commentRow);
                }

                if(empty($commentRows)) {
                    $commentHtml =  AppHtml::getNoComment();
                }


            }else {
                // no source message 
                $sourceHtml = AppHtml::getNoSource();
            }

            //include view 
            include(APP_WEB_DIR."/app/view/dashboard.tmpl");
    
        }

        private function process_select_page() {

            $gWeb = \com\indigloo\core\Web::getInstance();
            $qparams = Url::getRequestQueryParams();
            
            $loginId = Login::getLoginIdInSession();
            $loginDao = new \com\indigloo\fs\dao\Login();
            $access_token = $loginDao->getValidToken($loginId);

            if(empty($access_token)) {
                $error = "Your session has expired. Please login again!";
                $errors = array($error);
                $gWeb->store(Constants::FORM_MESSAGES,$errors);
               
                $fwd = "/ghost/canvas/login" ;
                header("location: ".$fwd);
                exit ;
            } 

            $pages = GraphAPI::getPages($access_token);
            $gWeb->store("fs.user.pages",$pages);
            $pageTableHtml = AppHtml::getPageTable($pages);

            $selfUrl = Url::current();

            $view = empty($pages) ? "/app/view/no-page.tmpl" : "/app/view/page-table.tmpl";
            $view = APP_WEB_DIR.$view ;
            include($view);

        }

        private function process_login() {
            $view = APP_WEB_DIR. '/app/view/login-dialog.tmpl' ;
            include ($view);
        }

        private function process_login_error() {
            $view = APP_WEB_DIR. '/app/view/login.tmpl' ;
            include ($view);
        }
        
        
    }
}
?>
