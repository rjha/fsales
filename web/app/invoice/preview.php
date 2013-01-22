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
    use \com\indigloo\fs\Constants as AppConstants ;

    $gWeb = \com\indigloo\core\Web::getInstance();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    $qparams = Url::getRequestQueryParams();
    $loginId = Login::getLoginIdInSession();

    // transfer encoded - decode to use!
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    $invoiceId = Url::tryQueryParam("invoice_id");
    $invoiceDao = new \com\indigloo\fs\dao\Invoice();
    
    // fetch invoice + post details
    $invoiceRow = $invoiceDao->getOnId2($invoiceId);
    if(empty($invoiceRow)) {
        $message = " No invoice  found for supplied invoice_id ";
        throw new UIException(array($message)) ;
    }
    
    $invoiceAllUrl = AppConstants::INVOICE_ALL_URL ;
    $invoiceState = $invoiceRow["op_bit"];

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Preview of invoice # <?php echo $invoiceRow["id"]; ?></title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>
         

    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
            <?php include(APP_WEB_DIR . '/app/inc/top-unit.inc'); ?>
            
            <div class="row">
                <div class="span8 offset1"> <?php FormMessage::render() ?> </div>
            </div>

            <div class="row">
                <div class="span8 offset1">

                    <div class="page-header">
                        &nbsp;
                    </div>

                    <?php echo AppHtml::getInvoicePreview($invoiceRow["op_bit"],$invoiceId);  ?>
                    <?php echo AppHtml::getInvoice($invoiceRow);  ?>
                    

                   

                </div>
            </div>
        </div> <!-- container -->

        <?php echo \com\indigloo\fs\util\Asset::version("/js/fs-bundle.js"); ?>
        
        <script type="text/javascript">

            $(document).ready(function(){
                
                $("a.invoice-mail").click(function(event){
                    
                    event.preventDefault();

                    var dataObj = {} ;
                    dataObj.params = {} ;
                    dataObj.params.invoiceId  = $(this).attr("id");
                    dataObj.endPoint = "/app/action/invoice/ajax/mail.php";
                    

                    var options = {
                        "dataType" : "json", 
                        "timeout" : 9000,
                        "messageDivId" : "#mail-message",
                        onDoneHandler : function (dataObj,response) {
                            
                            if(response.code == 200 ) {
                                var redirectUrl = '<?php echo $invoiceAllUrl; ?>' ;
                                window.location.replace(redirectUrl);
                            }
                        
                        }
                    };
                    
                    webgloo.fs.Ajax.post(dataObj,options) ;
 
                }) ;
            }) ;

        </script>
        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

