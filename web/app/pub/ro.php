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

    // @todo business rule 
    // orders with response code 100 cannot be tried again.
    // get invoice 
    
    

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


                // Rules
                // first name/ last name - min :3 max 30 | alphanumeric
                // first name <> last name
                // email : 64/ valid format
                // phone : digits only / max 16
                // address : 100 / min 6
                // city : 3-30 chars | alphabets only
                // state : required
                // pincode 2-12 : numbers only
                
                $("#form1").validate({
                    errorLabelContainer: $("#form-message"),
                    onkeyup:false,
                    
                    rules: {
                        first_name: {required: true, maxlength:30, minlength:3} ,
                        last_name : { required : true, maxlength : 30, minlength :2},
                        email: {required: true, email:true } ,
                        phone : {required : true, digits : true},
                        billing_address: {required: true , maxlength:100, minlength : 6} ,
                        billing_city: {required: true , maxlength:30, minlength:3} ,
                        billing_state: {required: true} ,
                        billing_pincode: {required: true , maxlength:12, minlength:2} ,

                        ship_first_name: {required: true, maxlength:30, minlength:3} ,
                        ship_last_name : { required : true, maxlength : 30, minlength :2},
                        ship_address: {required: true , maxlength:100, minlength : 6} ,
                        ship_city: {required: true , maxlength:30, minlength:3} ,
                        ship_state: {required: true} ,
                        ship_pincode: {required: true , maxlength:12, minlength:2}, 
                        ship_phone : {required : true, digits : true}
                        
                    },
                    messages: {
                        first_name: {
                            required: "First name is required ", 
                            maxlength : "Only 30 chars allowed in First Name", 
                            minlength: "At least 3 chars required in First Name"
                        } ,
                        last_name : { 
                            required : "Last name is required ",
                            maxlength : "Only 30 chars allowed in Last Name", 
                            minlength: "At least 2 chars required in Last Name"
                        },
                        ship_first_name: {
                            required: "First name (shipping) is required ", 
                            maxlength : "Only 30 chars allowed in First Name", 
                            minlength: "At least 3 chars required in First Name"
                        } ,
                        ship_last_name : { 
                            required : "Last name (shipping) is required ",
                            maxlength : "Only 30 chars allowed in Last Name", 
                            minlength: "At least 2 chars required in Last Name"
                        },
                        email: {
                            required: "Email is required", 
                            email : "Email is not in valid format" ,
                        } ,
                        phone : {
                            required : "Phone is required", 
                            digits : "Only numbers are allowed in Phone"
                        },
                        billing_address: {
                            required: "Address (billing) is required " , 
                            maxlength:"Only 100 chars allowed in Address",
                            minlength: "At least 6 chars required in  Address"
                        } ,
                        billing_city: {
                            required: true , 
                            maxlength : "Only 30 chars allowed in City (billing)", 
                            minlength: "At least 3 chars required in City(billing)"
                        } ,
                        billing_state: {
                            required: "State (billing) is required"
                        } ,
                        billing_pincode: {
                            required: "Pincode (billing) is required " , 
                            maxlength:"Only 12 chars allowed in Pincode (billing)",
                            minlength: "Atleast 2 chars required in Pincode (billing)"
                        },

                        ship_address: {
                            required: "Address (shipping) is required " , 
                            maxlength:"Only 100 chars allowed in Address",
                            minlength: "At least 6 chars required in  Address"
                        } ,
                        ship_city: {
                            required: true , 
                            maxlength : "Only 30 chars allowed in City (shipping)", 
                            minlength: "At least 3 chars required in City(shipping)"
                        } ,
                        ship_state: {
                            required: "State (shipping) is required"
                        } ,
                        ship_pincode: {
                            required: "Pincode (shipping) is required " , 
                            maxlength:"Only 12 chars allowed in Pincode (shipping)",
                            minlength: "Atleast 2 chars required in Pincode (shipping)"
                        },
                        ship_phone : {
                            required : "Phone (shipping) is required", 
                            digits : "Only numbers are allowed in Phone (shipping)"
                        }
                    }
                }); 

            });
        
        </script>

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

