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
    $loginId = Login::tryLoginIdInSession();

    // get access token
    // get comments for a source_id
    $sourceDao = new \com\indigloo\fs\dao\Source();
    $sources = $sourceDao->getOnLogin($loginId);
    $default_source_id = NULL ;

    if(!empty($sources)) {
        $default_source_id = $sources[0]["source_id"];
    }

    // sources html - no sources message
    // or render a choice of sources
    
    $sourceId = (isset($qparams["source_id"])) ? $qparams["source_id"] : $default_source_id;

    $sourceHtml = "" ;
    $commentHtml = "" ;

    if(!empty($sourceId)) {
        $sourceRow = $sourceDao->getOnId($sourceId);
        $commentDao = new \com\indigloo\fs\dao\Comment();
        $commentRows = $commentDao->getAll($sourceId);
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
        <title> User Dashboard page</title>
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
                        <h2> Dashboard </h2>
                    </div>
                    <?php echo $sourceHtml; ?>
                    <?php echo $commentHtml; ?>
                    
                    
                </div>

            </div>

            
        </div>        

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

