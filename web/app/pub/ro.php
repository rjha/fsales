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

    $orderId = Url::tryQueryParam("order_id");
    $orderId = empty($orderId) ? 0 : $orderId ;
    settype($orderId, "integer");
    if(empty($orderId)) {
        // show 400 bad request
        $controller = new \com\indigloo\fs\controller\Http400();
        $controller->process();
        exit;
    }

    $orderDao = new \com\indigloo\fs\dao\Order(); 
    $orderRow = $orderDao->getOnId($orderId);
    
    if(empty($orderRow)) {
        // show 404
        $controller = new \com\indigloo\fs\controller\Http404();
        $controller->process();
        exit;
    }

    $op_bit = $orderRow["op_bit"];
    settype($op_bit,"integer");

    if($op_bit != AppConstants::ORDER_NEW_STATE) {
        // you cannot edit this order now!
        $message = "This order has already been processed." ;
        echo AppHtml::messageBox($message);
        exit ;
    }

    // get Invoice Html
    $invoiceId = $orderRow["invoice_id"];
    $invoiceDao = new \com\indigloo\fs\dao\Invoice(); 
    // get invoice + post details
    $invoiceRow = $invoiceDao->getOnId2($invoiceId);
    if(empty($invoiceRow)) {
        $message = "Error: no invoice found for this order!" ;
        echo AppHtml::messageBox($message);
        exit ;
    }

    $invoiceHtml = AppHtml::getCheckoutInvoice($invoiceRow);

    // tamper-proof our form
    $checkout_token  = Util::getMD5GUID() ;
    $gWeb->store("fs:reorder:token",$checkout_token);

    $formString =  sprintf("%d:%s",$orderId,$checkout_token ) ;
    $checksum = hash_hmac("sha256", $formString , AppConstants::SECRET_KEY);


    

?>
<!DOCTYPE html>
<html>

    <head>
        <title> Retry order # <?php echo $orderId ?> </title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>
         

    </head>

    <body>
        
        <?php include(APP_WEB_DIR . '/app/inc/toolbar/default.inc'); ?>
        
        <div class="container mh600">
            
            <div class="row">
                <div class="span8 offset1">
                    <h3>Retry order # <?php echo $orderId; ?> </h3>
                    <?php echo $invoiceHtml;  ?>
                </div>
            </div> <!-- row:1 -->

            <div class="row">
                <div class="span8 offset1">
                    <?php FormMessage::render() ?>
                    
                    <!-- zaakpay specific form -->
                    <div class="form-wrapper">
                        <div id="form-message"> </div>
                        <form  id="form1"  name="form1" action="/app/action/order/retry.php"  method="POST">
                            <table class="form-table">
                                
                                <tr>
                                    <td> <label>First name*</label> </td>
                                    <td>
                                        <input type="text" name="first_name" maxlength="30" value="<?php echo $sticky->get('first_name',$orderRow['first_name']); ?>" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td> <label>Last name*</label> </td>
                                    <td>
                                        <input type="text" name="last_name" maxlength="30" value="<?php echo $sticky->get('last_name',$orderRow['last_name']); ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td> <label>Email*</label> </td>
                                    <td>
                                        <input type="text" name="email" maxlength="64" value="<?php echo $sticky->get('email',$orderRow['email']); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Phone*</label> </td>
                                    <td>
                                        <input type="text" name="phone" maxlength="16" value="<?php echo $sticky->get('phone',$orderRow['phone']); ?>" />
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
                                        <input type="text" name="billing_address" maxlength="100" value="<?php echo $sticky->get('billing_address',$orderRow['billing_address']); ?>" />
                                    </td>
                                </tr>
                                   
                                <tr>
                                    <td> &nbsp; </td>
                                    <td> 
                                        <span>City*</span>&nbsp;
                                        <input type="text" name="billing_city" style="width:150px;" maxlength="30" value="<?php echo $sticky->get('billing_city',$orderRow['billing_city']); ?>" />
                                    
                                        <span>Pincode*</span>&nbsp;
                                        <input type="text" name="billing_pincode" style="width:80px;" maxlength="16" value="<?php echo $sticky->get('billing_pincode',$orderRow['billing_pincode']); ?>" />
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
                                        <input type="text" name="ship_first_name" maxlength="30" value="<?php echo $sticky->get('ship_first_name',$orderRow['shipping_first_name']); ?>" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td> <label>Last name*</label> </td>
                                    <td>
                                        <input type="text" name="ship_last_name" maxlength="30" value="<?php echo $sticky->get('ship_last_name',$orderRow['shipping_last_name']); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Phone*</label> </td>
                                    <td>
                                        <input type="text" name="ship_phone" maxlength="16" value="<?php echo $sticky->get('ship_phone',$orderRow['shipping_phone']); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Address*</label> </td>
                                    <td>
                                        <input type="text" name="ship_address" maxlength="100" value="<?php echo $sticky->get('ship_address',$orderRow['shipping_address']); ?>" />
                                    </td>
                                </tr>
                                   

                                <tr>
                                    <td> &nbsp; </td>
                                    <td> 
                                        <span>City*</span>&nbsp;
                                        <input type="text" name="ship_city" style="width:150px;" maxlength="30" value="<?php echo $sticky->get('ship_city',$orderRow['shipping_city']); ?>" />
                                    
                                        <span>Pincode*</span>&nbsp;
                                        <input type="text" name="ship_pincode" style="width:80px;" maxlength="16" value="<?php echo $sticky->get('ship_pincode',$orderRow['shipping_pincode']); ?>" />
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
                                            <button class="btn btn-success btn-large" type="submit" name="save" value="Save">Proceed to pay</button>
                                            <div>
                                                <span>secure with</span>
                                                <img src="http://www.zaakpay.com/images/paybtn-blk.gif" alt="" align="absmiddle" /> zaakpay
                                            </div>
                                           
                                        </div>

                                    </td>
                                   
                                </tr>

                            </table> 

                            <input type="hidden" name="order_id" value="<?php echo $orderId; ?>" /> 
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

