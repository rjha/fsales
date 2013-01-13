<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\html\Application as AppHtml ;


    $gWeb = \com\indigloo\core\Web::getInstance();
    $invoiceId = Url::tryQueryParam("invoice_id");
    
    if(empty($invoiceId)) {
        // show 400 bad request
        $controller = new \com\indigloo\fs\controller\Http400();
        $controller->process();
        exit;
    }

    $invoiceId = urldecode($invoiceId);
    settype($invoiceId, "integer");

    $invoiceDao = new \com\indigloo\fs\dao\Invoice(); 
    // get invoice + post details
    $invoiceRow = $invoiceDao->getOnId2($invoiceId);
    
    if(empty($invoiceRow)) {
        // show 404
        $controller = new \com\indigloo\fs\controller\Http404();
        $controller->process();
        exit;
    }

    $invoiceHtml = AppHtml::getInvoice3($invoiceRow);
    //@todo : make the form tampering proof
?>
<!DOCTYPE html>
<html>

    <head>
        <title> Invoice for <?php echo $invoiceRow["name"]; ?> </title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>
         

    </head>

    <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar/default.inc'); ?>
        
        <div class="container mh600">
              
            <div class="row">
                <div class="span8 offset1"> <?php FormMessage::render() ?> </div>
            </div>

            <div class="row">
                <div class="span8 offset1">

                    <div class="page-header">
                        <h3> Invoice for <?php echo $invoiceRow["name"]; ?> </h3>
                    </div>
                    <?php echo $invoiceHtml;  ?>
                    

                    <div class="section">
                        <form  id="form1"  name="form1" action="/app/action/zaakpay/tx.php"  method="POST">
                            
                            <button class="btn btn-success" type="submit" name="save" value="Save">Mail Invoice</button>
                              
                            <input type="hidden" name="invoice_id" value="<?php echo $invoiceRow['id']; ?>" /> 
                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />

                        </form>
                    </div>

                </div>
            </div>
        </div> <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

