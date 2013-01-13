<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');


    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\html\Application as AppHtml ;


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
    $pageBaseURI ="/app/dashboard.php" ;
    
     
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
    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> User Dashboard</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h3> Comments </h3>
                        <?php echo $sourceHtml; ?>
                    </div>
                    
                    <?php FormMessage::render() ?>
                    <?php echo $commentHtml; ?>
                    
                    
                </div>

            </div>

            
        </div>
        
        <?php $paginator->render($pageBaseURI,$startId,$endId,$gNumRecords);  ?>
         
        <?php echo \com\indigloo\fs\util\Asset::version("/js/fs-bundle.js"); ?>
        <script type="text/javascript">
            
            $(document).ready(function(){
                $("a.open-panel").click(function(event) {

                    var divId = '#' + $(this).attr("rel");
                    //hide any open panels
                    $('.panel').hide();
                    //hide page message
                    $("#page-message").html('');
                    $("#page-message").hide();
                    // show target panel
                    $(divId).show("slow");
                });

                $("a.close-panel").click(function(event) {
                    var divId = '#' + $(this).attr("rel");
                    $(divId).hide("slow");
                    //hide page message as well
                    $("#page-message").html('');
                    $("#page-message").hide();
                });
              
            });
        </script>


        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

