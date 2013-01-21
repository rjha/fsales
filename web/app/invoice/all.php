<?php

    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');
    include (APP_WEB_DIR.'/app/inc/role/user.inc');


    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\exception\UIException;

    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\html\Application as AppHtml ;


    $gWeb = \com\indigloo\core\Web::getInstance();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    $qparams = Url::getRequestQueryParams();
    $loginId = Login::getLoginIdInSession();

    $pageSize = 11 ;
    $paginator = new \com\indigloo\ui\Pagination($qparams,$pageSize);
    $paginator->setBaseConvert(false);

    $startId = NULL;
    $endId = NULL;
    $gNumRecords = 0 ;
    $pageBaseURI ="/app/invoice/all.php" ;

    // get all invoices for this user
    $invoiceDao = new \com\indigloo\fs\dao\Invoice();
    $invoiceRows = $invoiceDao->getPaged($loginId,$paginator);

    // fix pagination variables
    $gNumRecords = sizeof($invoiceRows) ;
    $invoiceHtml = "" ;

    if ($gNumRecords > 0) {
        $startId = $invoiceRows[0]["id"];
        $endId = $invoiceRows[$gNumRecords - 1]["id"];
        foreach($invoiceRows as $invoiceRow) {
            $invoiceHtml .= AppHtml::getInvoice($invoiceRow);
        }
    } else {
        $invoiceHtml = AppHtml::getNoInvoice();
    }

    $selfUrl = Url::current();

?>

<!DOCTYPE html>
<html>

    <head>
        <title> User invoices</title>
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
                        <h3> Invoices </h3>
                       
                    </div>
                    
                    <?php FormMessage::render() ?>
                    <?php echo $invoiceHtml; ?>
                    
                    
                </div>

            </div>

            
        </div>
        
        <?php $paginator->render($pageBaseURI,$startId,$endId,$gNumRecords);  ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/js/fs-bundle.js"); ?>
        
        <script type="text/javascript">

            $(document).ready(function(){
                
                var callback = {
                    
                    mail : function(dataObj,response) { 
                        // get fresh copy from server
                        window.location.reload(true);
                    } ,
                    edit : function(dataObj,response) {
                        //go to invoice edit URL
                        var qUrl = '<?php echo base64_encode($selfUrl) ?>' ;
                        var editUrl = "/app/invoice/edit.php?invoice_id=" 
                                + dataObj.params.invoiceId 
                                + "&q=" + qUrl ;
                        window.location.replace(editUrl);

                    },
                    cancel : function (dataObj,response) {
                        // reload to reflect changed state.
                    } ,
                    shipping: function(dataObj,response) {
                        // turn off the ajax message
                        var invoiceId = dataObj.params.invoiceId ;
                        $("#invoice-ajax-"+invoiceId).html("") ;

                        //load popup
                        $("#simple-popup #content").html('');
                        $("#simple-popup #content").html(response);

                        /* show mask */
                        var maskHeight = $(document).height();
                        var maskWidth = $(window).width();

                        $("#popup-mask").css({'width':maskWidth,'height':maskHeight});
                        $("#popup-mask").show();

                        /* show popup */
                        $("#simple-popup").show();

                    }
                }

                webgloo.fs.invoice.initActions(callback);

                $("a#simple-popup-close").click(function(event) {
                    event.preventDefault();
                    $("#simple-popup #content").html('');
                    $("#simple-popup").hide();
                    $("#popup-mask").hide();
                });

            }) ;

        </script>

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

        <!-- simple popup -->
        <div id="simple-popup">
            <div id="header">
             <div class="wrapper">
                <div class="floatr">
                    <a id="simple-popup-close" href="">close&nbsp;<i class="icon-remove"> </i></a>
                </div>
            </div>
            <div class="clear"> </div>
         </div>
        <div id="content"> </div>

        </div>

        <div id="popup-mask"> </div>
        
    </body>
</html>
