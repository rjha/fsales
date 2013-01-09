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
     
    $sourceHtml = "" ;
    $commentHtml = "" ;

    //pagination variables
    $startId = NULL;
    $endId = NULL;
    $gNumRecords = 0 ;
    $pageBaseURI ="/app/dashboard.php" ;

    if(!empty($sourceId)) {
        $sourceRow = $sourceDao->getOnId($sourceId);

        //pagination
        $pageSize = 10 ;
        $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
        $paginator->setBaseConvert(false);

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
        <?php echo \com\indigloo\fs\util\Asset::version("/css/bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <div class="page-header">
                        <h3> Dashboard </h3>
                        <?php echo $sourceHtml; ?>
                    </div>
                   
                    <?php echo $commentHtml; ?>
                    
                    
                </div>

            </div>

            
        </div>
        
        <?php $paginator->render($pageBaseURI,$startId,$endId,$gNumRecords);  ?>
         
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"> </script>
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

