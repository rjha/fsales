<?php
namespace com\indigloo\fs\controller{

     
    use \com\indigloo\Util as Util;
    use \com\indigloo\Url as Url;
    use \com\indigloo\Constants as Constants;
    use \com\indigloo\Logger as Logger ;

    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\html\Application as AppHtml ;
    use \com\indigloo\fs\api\Graph as GraphAPI ;
    use \com\indigloo\fs\Constants as AppConstants ;

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

        private function login_check() {
            if(!Login::hasSession()) {
                $fwd = AppConstants::WWW_LOGIN_URL ;
                header('location: '.$fwd);
                exit ;
            }
        }

        private function process_home() {
            $view = APP_WEB_DIR. '/app/view/home.tmpl' ;
            include ($view);
        }

        private function process_dashboard() {
            // dashboard needs login 
            $this->login_check();
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

            $sourceRow = $sourceDao->getOnId($sourceId);

            if(empty($sourceRow)) {
                $sourceHtml = AppHtml::getNoSource();
                include(APP_WEB_DIR."/app/view/dashboard.tmpl");
                return ;
            }

            // ft := [verb | all]
            // by default show only comments with verb
            $ft = (isset($qparams["ft"])) ? $qparams["ft"] : AppConstants::ALL_COMMENT_FILTER;

            //pagination variables
            
            $pageSize = 10 ;
            $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
            $paginator->setBaseConvert(false);

            $startId = NULL;
            $endId = NULL;
            $gNumRecords = 0 ;
            $pageBaseURI ="/ghost/canvas/dashboard" ;
            
            $commentDao = new \com\indigloo\fs\dao\Comment();
            $commentRows = $commentDao->getPaged($sourceId,$ft,$paginator);
            $sourceHtml = AppHtml::getSource($sourceRow,$sources,$qparams);

            //fix pagination variables
            $gNumRecords = sizeof($commentRows) ;
            if ($gNumRecords > 0) {
                $startId = $commentRows[0]["created_ts"];
                $endId = $commentRows[$gNumRecords - 1]["created_ts"];
            }

            foreach($commentRows as $commentRow) {
                $commentHtml .= AppHtml::getComment($commentRow);
            }

            if(empty($commentRows)) {
                $commentHtml =  AppHtml::getNoComment($sourceRow);
            }

            include(APP_WEB_DIR."/app/view/dashboard.tmpl");
            return ;
        
    
        }

        /*
         * display of /ghost/canvas/select-page is controlled by 
         * fs_facebook_user.op_bit flag. op_bit state corresponds to 
         * steps completed in login workflow. possible values of op_bit is
         * 1|2. we only process this page to show user pages 
         *
         * when no user pages :- no-page message
         * when only one page - one page in DIV
         * when multiple pages - table with selection 
         * 
         *
         */
        private function process_select_page() {

            try {
                $this->login_check();
                $gWeb = \com\indigloo\core\Web::getInstance();
                
                $loginId = Login::getLoginIdInSession();
                $loginDao = new \com\indigloo\fs\dao\Login();
                $access_token = $loginDao->getValidToken($loginId);
               
                if(empty($access_token)) {
                    // page fetch needs access token
                    $fwd = AppConstants::WWW_LOGIN_ERROR_URL ;
                    header("location: ".$fwd);
                    exit ;
                }

                $fbPages = GraphAPI::getPages($access_token);
                $num_pages = sizeof($fbPages);

                if($num_pages == 0 ) {
                    $view = APP_WEB_DIR."/app/view/no-page.tmpl" ;
                    include($view);
                } else if($num_pages == 1 ) {
                    // store $page in DB
                    // flip fs_facebook_user.op_bit to 2
                    $streamDao = new \com\indigloo\fs\dao\Stream();
                    $streamDao->addSources($loginId,$fbPages);
                    $view = APP_WEB_DIR."/app/account-done.php" ;
                    include($view);
                }else {
                    $gWeb->store("fs.user.pages",$fbPages);
                    $view = APP_WEB_DIR."/app/view/page-table.tmpl" ;
                    include($view);
                }

            } catch(\Exception $ex) {
                // Error during Account setup
                Logger::getInstance()->error($ex->getMessage());
                Logger::getInstance()->backtrace($ex->getTrace());

                $view = APP_WEB_DIR."/app/account-error.php" ;
                include($view);
            }
                       
            
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
