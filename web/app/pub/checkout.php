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


    $gWeb = \com\indigloo\core\Web::getInstance();
    $sticky = new Sticky($gWeb->find(Constants::STICKY_MAP,true));
    
    // transfer encoded - decode to use!
    $qUrl = Url::tryBase64QueryParam("q", "/");
    $fUrl = base64_encode(Url::current());
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

    $invoiceHtml = AppHtml::getCheckoutInvoice($invoiceRow);
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
        <style>
            /* @inpage @hardcoded */
            #invoice-item img {
                width: auto;
                height: auto;
                max-width: 320px ;
                border: 5px solid #f5f5f5;
                
            }

        </style>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar/default.inc'); ?>
        
        <div class="container mh600">
            
            <div class="row">
                <div class="span5 offset1">
                    <?php echo $invoiceHtml;  ?>
                </div>
                <div class="span6">

                    <div class="page-header">
                        <h3> Invoice for <?php echo $invoiceRow["name"]; ?> </h3>
                    </div>
                    <div class="row">
                        <?php FormMessage::render() ?>
                    </div>

                    <!-- zaakpay specific form -->
                    <div class="form-wrapper">
                        <div id="form-message"> </div>
                        <form  id="form1"  name="form1" action="/app/action/order/new.php"  method="POST">
                            <table class="form-table">
                                
                                <tr>
                                    <td> <label>First Name *</label>
                                        <input type="text" name="first_name" maxlength="30" value="<?php echo $sticky->get('first_name'); ?>" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td> <label>Last Name *</label>
                                        <input type="text" name="last_name" maxlength="30" value="<?php echo $sticky->get('last_name'); ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <td> <label>Email *</label>
                                        <input type="text" name="email" maxlength="64" value="<?php echo $sticky->get('email'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Phone*</label>
                                    <input type="text" name="phone" maxlength="16" value="<?php echo $sticky->get('phone'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Billing Address *</label>
                                    <input type="text" name="billing_address" maxlength="100" value="<?php echo $sticky->get('billing_address'); ?>" />
                                    </td>
                                </tr>
                                   

                                <tr>
                                    <td> <span>City*</span>&nbsp;
                                    <input type="text" name="billing_city" style="width:200px;" maxlength="30" value="<?php echo $sticky->get('billing_city'); ?>" />

                                     <span>Pincode*</span>&nbsp;
                                        <input type="text" style="width:110px;" name="billing_pincode" maxlength="16" value="<?php echo $sticky->get('billing_pincode'); ?>" />
                                    </td>
                                </tr> 

                                <tr>
                                    <td> 
                                        <span>State*</span>
                                        <?php $gUIStateId = "billing_state" ; include(APP_WEB_DIR. "/app/inc/data/state.inc"); ?>
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

        <?php echo \com\indigloo\fs\util\Asset::version("/js/fs-bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){
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
                    rules: {
                        first_name: {required: true, maxlength:30, minlength:3} ,
                        last_name : { required : true, maxlength : 30, minlength :2},
                        email: {required: true, email:true } ,
                        phone : {required : true, digits : true},
                        billing_address: {required: true , maxlength:100, minlength : 6} ,
                        billing_city: {required: true , maxlength:30, minlength:3} ,
                        billing_state: {required: true} ,
                        billing_pincode: {required: true , maxlength:12, minlength:2} 
                        
                    },
                    messages: {
                        first_name: {
                            required: "First Name is required ", 
                            maxlength : "Only 30 chars allowed in First Name", 
                            minlength: "At least 3 chars required in First Name"
                        } ,
                        last_name : { 
                            required : "Last Name is required ",
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
                        } 
                    }
                }); 

            });
        
        </script>

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

