<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;

    use \com\indigloo\fs\auth\Login as Login ;
    use \com\indigloo\fs\html\Application as AppHtml ;


    $gWeb = \com\indigloo\core\Web::getInstance();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));

    // transfer encoded - decode to use!
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());
    $loginId = Login::getLoginIdInSession();
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
                    

                    <div class="form-wrapper">
                        <form  id="form1"  name="form1" action="/app/action/zaakpay/tx.php"  method="POST">
                            <table class="form-table">
                                
                                <tr>
                                    <td> <label>First Name *</label>
                                        <input type="text" name="first_name" maxlength="64" value="" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td> <label>Last Name *</label>
                                        <input type="text" name="last_name" maxlength="64" value="" />
                                    </td>
                                </tr>

                                <tr>
                                    <td> <label>Email *</label>
                                        <input type="text" name="email" maxlength="64" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Phone*</label>
                                    <input type="text" name="phone" maxlength="100" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Billing Address *</label>
                                    <input type="text" name="billing_address" maxlength="100" value="" />
                                    </td>
                                </tr>
                                   

                                <tr>
                                    <td> <span>City*</span>&nbsp;
                                    <input type="text" name="billing_city" style="width:200px;" maxlength="100" value="" />
                                     <span>Pincode*</span>&nbsp;
                                        <input type="text" style="width:110px;" name="billing_pincode" maxlength="100" value="" />
                                    </td>
                                </tr> 

                                <tr>
                                    <td> 
                                        <span>State*</span>
                                        <?php include(APP_WEB_DIR. "/app/inc/data/state.inc"); ?>
                                    </td>
                                </tr>
       

                                

                                <tr>
                                    <td>
                                        <div class="form-actions">
                                            <button class="btn btn-success btn-large" type="submit" name="save" value="Save">Proceed to pay</button>
                                            <div>
                                                <span>secure with</span>
                                                <img src="http://www.zaakpay.com/images/paybtn-blk.gif" alt="" align="absmiddle" /> zaakpay
                                            </div>
                                           
                                        </div>

                                    </td>
                                   
                                </tr>

                            </table> 
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

