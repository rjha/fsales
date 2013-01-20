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

    $pageSize = 10 ;
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


            function mail_invoice(invoiceId) {
                var dataObj = {} ;
                dataObj.params = {} ;
                dataObj.params.invoiceId  = invoiceId;
                dataObj.endPoint = "/app/action/invoice/ajax-mail.php";
                
                var options = {
                    "dataType" : "json", 
                    "timeout" : 9000,
                    "messageDivId" : "#invoice-ajax-" + invoiceId,
                    onDoneHandler : function (response) {
                        $("#invoice-ajax-"+invoiceId).html(response.message);
                    }
                };
                
                webgloo.fs.Ajax.post(dataObj,options) ;
            }

            function edit_invoice (invoiceId) {
                // self URL is q URL
                alert('invoice edited');
            }

            function cancel_invoice (invoiceId) {
                var dataObj = {} ;
                dataObj.params = {} ;
                dataObj.params.invoiceId  = invoiceId;
                dataObj.params.action  =  "cancel";
                dataObj.endPoint = "/app/action/invoice/ajax-command.php";
                
                var options = {
                    "dataType" : "json", 
                    "timeout" : 9000,
                    "messageDivId" : "#invoice-ajax-" + invoiceId
                };
                
                webgloo.fs.Ajax.post(dataObj,options) ;
            }

            function ship_invoice (invoiceId) {
                alert('invoice shipped');
            }

            function remind_invoice (invoiceId) {
                var dataObj = {} ;
                dataObj.params = {} ;
                dataObj.params.invoiceId  = invoiceId;
                dataObj.params.action  =  "remind";
                dataObj.endPoint = "/app/action/invoice/ajax-command.php";
                
                var options = {
                    "dataType" : "json", 
                    "timeout" : 9000,
                    "messageDivId" : "#invoice-ajax-" + invoiceId
                };
                
                webgloo.fs.Ajax.post(dataObj,options) ;
            }

            $(document).ready(function(){
                
                $("a.invoice-action").live("click",function(event){
                    
                    event.preventDefault();
                    
                    var invoiceId = $(this).attr("id");
                    var action =  $(this).attr("rel") ;

                     switch(action) {
                        case 'mail' :
                            mail_invoice(invoiceId);
                            break ;
                        case 'edit' :
                            edit_invoice(invoiceId);
                            break ;
                        case 'cancel' :
                            cancel_invoice(invoiceId);
                            break ;
                        case 'shipping' :
                            ship_invoice(invoiceId);
                            break ;
                        case 'reminder' :
                            remind_invoice(invoiceId);
                            break ;
                        default :
                            break ;

                    }
 
                }) ;
            }) ;

        </script>

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>
