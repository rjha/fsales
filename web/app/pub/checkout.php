<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    use com\indigloo\Util;
    use com\indigloo\Url as Url;
    use com\indigloo\Constants as Constants;
    use com\indigloo\Configuration as Config;
    
    use \com\indigloo\ui\form\Message as FormMessage;
    use \com\indigloo\ui\form\Sticky;
    use \com\indigloo\fs\html\Application as AppHtml ;
    use \com\indigloo\fs\Constants as AppConstants ;


    $gWeb = \com\indigloo\core\Web::getInstance();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    
    // transfer encoded - decode to use!
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());
    
    $invoiceId = Url::tryQueryParam("invoice_id");
    $invoiceId = empty($invoiceId) ? 0 : $invoiceId ;
    settype($invoiceId, "integer");

    if(empty($invoiceId)) {
        // show 400 bad request
        $controller = new \com\indigloo\fs\controller\Http400();
        $controller->process();
        exit;
    }

    $invoiceDao = new \com\indigloo\fs\dao\Invoice(); 
    // get invoice + post details
    $invoiceRow = $invoiceDao->getOnId2($invoiceId);
    
    if(empty($invoiceRow)) {
        // show 404
        $controller = new \com\indigloo\fs\controller\Http404();
        $controller->process();
        exit;
    }

    $p_order_id = $invoiceRow["p_order_id"];
    $p_order_id = empty($p_order_id) ? 0 : $p_order_id ;
    settype($p_order_id, "integer");

    $op_bit = $invoiceRow["op_bit"];
    settype($op_bit,"integer");

    // route to retry order page if op_bit = 3 (processing )
    // and p_order_id is not empty


    if(($p_order_id > 0) && ($op_bit == AppConstants::INVOICE_PROCESSING_STATE)) {
        
        $params = array("order_id" => $p_order_id);
        $fwd = Url::createUrl("/app/pub/ro.php",$params) ;
        //Location:<space>
        header("Location: ".$fwd); 
        exit ;
    }

    // error if op_bit != 2
    if($op_bit != AppConstants::INVOICE_PENDING_STATE) {
        $message = "This invoice is under processing and cannot be changed." ;
        echo AppHtml::messageBox($message);
        exit ;
    }

    // op_bit = 2 | show screen
    $invoiceHtml = AppHtml::getCheckoutInvoice($invoiceRow);
    // make the form tampering proof
    
    $checkout_token  = Util::getMD5GUID() ;
    $gWeb->store("fs:checkout:token",$checkout_token);

    $formString =  sprintf("%d:%s",$invoiceId,$checkout_token ) ;
    $checksum = hash_hmac("sha256", $formString , AppConstants::SECRET_KEY);


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
                <div class="span8 offset1">
                    <h3> Invoice for <?php echo $invoiceRow["name"]; ?> </h3>
                    <?php echo $invoiceHtml;  ?>
                </div>
            </div> <!-- row:1 -->

            <div class="row">
                <div class="span8 offset1">
                    <?php FormMessage::render() ?>
                    
                    <!-- zaakpay specific form -->
                    <div class="form-wrapper">
                        <div id="form-message"> </div>
                        <form  id="form1"  name="form1" action="/app/action/order/new.php"  method="POST">
                            <table class="form-table">
                                
                                <tr>
                                    <td> <label>First name*</label> </td>
                                    <td>
                                        <input type="text" name="first_name" maxlength="30" value="<?php echo $sticky->get('first_name'); ?>" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td> <label>Last name*</label> </td>
                                    <td>
                                        <input type="text" name="last_name" maxlength="30" value="<?php echo $sticky->get('last_name'); ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td> <label>Email*</label> </td>
                                    <td>
                                        <input type="text" name="email" maxlength="64" value="<?php echo $sticky->get('email'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Phone*</label> </td>
                                    <td>
                                        <input type="text" name="phone" maxlength="16" value="<?php echo $sticky->get('phone'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> &nbsp; </td>
                                    <td>
                                        <b> Billing Address </b>
                                    </td>
                                </tr>

                                <tr>
                                    <td> <label>Address*</label> </td>
                                    <td>
                                        <input type="text" name="billing_address" maxlength="100" value="<?php echo $sticky->get('billing_address'); ?>" />
                                    </td>
                                </tr>
                                   
                                <tr>
                                    <td> &nbsp; </td>
                                    <td> 
                                        <span>City*</span>&nbsp;
                                        <input type="text" name="billing_city" style="width:150px;" maxlength="30" value="<?php echo $sticky->get('billing_city'); ?>" />
                                    
                                        <span>Pincode*</span>&nbsp;
                                        <input type="text" name="billing_pincode" style="width:80px;" maxlength="16" value="<?php echo $sticky->get('billing_pincode'); ?>" />
                                    </td>
                                </tr> 

                                <tr>
                                    <td> &nbsp; </td>
                                    <td>
                                        <span>State*</span> 
                                        <?php $gUIStateId = "billing_state" ; include(APP_WEB_DIR. "/app/inc/data/state.inc"); ?>
                                    </td>
                                </tr>
                                    
                                <!-- shipping -->

                                <tr>
                                    <td> &nbsp; </td>
                                    <td>
                                        <b> Shipping Address </b> 
                                        
                                        <a id="copy-billing-link" href="#" class="btn btn-small btn-info"> copy billing address </a>
                                    </td>
                                </tr>
                                 <tr>
                                    <td> <label>First name*</label> </td>
                                    <td>
                                        <input type="text" name="ship_first_name" maxlength="30" value="<?php echo $sticky->get('ship_first_name'); ?>" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td> <label>Last name*</label> </td>
                                    <td>
                                        <input type="text" name="ship_last_name" maxlength="30" value="<?php echo $sticky->get('ship_last_name'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Phone*</label> </td>
                                    <td>
                                        <input type="text" name="ship_phone" maxlength="16" value="<?php echo $sticky->get('phone'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Address*</label> </td>
                                    <td>
                                        <input type="text" name="ship_address" maxlength="100" value="<?php echo $sticky->get('ship_address'); ?>" />
                                    </td>
                                </tr>
                                   

                                <tr>
                                    <td> &nbsp; </td>
                                    <td> 
                                        <span>City*</span>&nbsp;
                                        <input type="text" name="ship_city" style="width:150px;" maxlength="30" value="<?php echo $sticky->get('ship_city'); ?>" />
                                    
                                        <span>Pincode*</span>&nbsp;
                                        <input type="text" name="ship_pincode" style="width:80px;" maxlength="16" value="<?php echo $sticky->get('ship_pincode'); ?>" />
                                    </td>
                                </tr> 

                                <tr>
                                    <td> &nbsp; </td>
                                    <td>
                                        <span>State*</span> 
                                        <?php $gUIStateId = "ship_state" ; include(APP_WEB_DIR. "/app/inc/data/state.inc"); ?>
                                    </td>
                                </tr>


                                <tr>
                                    <td> &nbsp;</td>
                                    <td>
                                        <div class="form-actions">
                                             <input type="submit" class="btn-mobikwik-wallet" style="height:73px;" value=""/>
                                        </div>

                                    </td>
                                   
                                </tr>

                            </table> 

                            <input type="hidden" name="invoice_id" value="<?php echo $invoiceRow['id']; ?>" /> 
                            <input type="hidden" name="checkout_token" value="<?php echo $checkout_token ?>" />
                            <input type="hidden" name="checksum" value="<?php echo $checksum ?>" />
                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />

                        </form>
                    </div>

                </div>
             
            </div> <!-- row:2 -->
        </div> <!-- container -->

        <?php echo \com\indigloo\fs\util\Asset::version("/js/fs-bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){

                
                $("#copy-billing-link").click( function(event) {
                    event.preventDefault();
                    
                    var frm1 = document.forms["form1"];
                    frm1.ship_first_name.value = frm1.first_name.value ;
                    frm1.ship_last_name.value = frm1.last_name.value ;

                    frm1.ship_address.value = frm1.billing_address.value ;
                    frm1.ship_city.value = frm1.billing_city.value ;
                    frm1.ship_pincode.value = frm1.billing_pincode.value ;
                    frm1.ship_state.value = frm1.billing_state.value ;
                    frm1.ship_phone.value = frm1.phone.value ;

                });

                $("#form1").validate(webgloo.fs.OrderValidator); 

            });
        
        </script>

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

