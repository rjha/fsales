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

    // transfer encoded - decode to use!
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());

    $invoiceId = Url::tryQueryParam("invoice_id");
    
    $invoiceDao = new \com\indigloo\fs\dao\Invoice();
    $commentDao = new \com\indigloo\fs\dao\Comment();
    
    $invoiceRow = $invoiceDao->getOnId($invoiceId);
    if(empty($invoiceRow)) {
        $message = " No invoice  found for supplied invoice_id ";
        throw new UIException(array($message)) ;
    }

    $commentId = $invoiceRow["comment_id"];
    $commentRow = $commentDao->getOnId($commentId);
    $invoiceHtml = AppHtml::getInvoice($invoiceRow,$commentRow);
    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> Invoice # <?php echo $invoiceRow["id"]; ?></title>
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
                        <h3> Invoice # <?php echo $invoiceRow["id"]; ?> </h3>
                    </div>
                    <?php echo $invoiceHtml;  ?>
                    

                    <div class="section">
                        <form  id="form1"  name="form1" action="/app/action/invoice/mail.php"  method="POST">
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

