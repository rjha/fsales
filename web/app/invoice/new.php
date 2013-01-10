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

    $commentId = Url::tryQueryParam("comment_id");
    $commentDao = new \com\indigloo\fs\dao\Comment();
    $commentRow = $commentDao->getOnId($commentId);
    $commentHtml = AppHtml::getComment($commentRow, array("invoice" => false));
    
    if(empty($commentRow)) {
        $message = " No comment found for invoice ";
        throw new UIException(array($message)) ;
    }

?>

<!DOCTYPE html>
<html>

    <head>
        <title> New Invoice</title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>
        <style>
            /* @inpage @hardcoded */
            .widget { margin-bottom:20px;}
        </style>

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
                        <h3> Create invoice </h3>
                    </div>

                    <?php echo $commentHtml; ?>
                    <div class="form-wrapper">
                        
                        <div id="form-message"> </div>
                        <form  id="form1"  name="form1" action="/app/action/invoice/new.php"  method="POST">
                          
                            <table class="form-table">
                                
                                <tr>
                                    <td> <label>Buyer Name *</label>
                                        <input type="text" name="name" maxlength="64" value="<?php echo $sticky->get('name',$commentRow['user_name']); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> <label>Buyer Email *</label>
                                        <input type="text" name="email" maxlength="64" value="<?php echo $sticky->get('email'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>  
                                        <!-- @inpage @hardcoded -->
                                        <span>Quantity *</span>
                                        <input type="text"  name="quantity" maxlength="4" value="<?php echo $sticky->get('quantity',1); ?>" style="width:30px;" />
                                        
                                        <span>Price *</span>
                                        <input type="text"  name="price" maxlength="10" value="<?php echo $sticky->get('price'); ?>" style="width:90px;" />
                                        
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>
                                        <label>Any other info &nbsp;(max 512 chars)</label>
                                        <textarea  id="selller_info" maxlength="512" name="seller_info" style="height:110px;" cols="50" rows="4" ></textarea>
                                         
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-actions">
                                            <button class="btn btn-success" type="submit" name="save" value="Save">Submit</button>
                                            <a href="<?php echo base64_decode($qUrl); ?>"> <button class="btn" type="button" name="cancel">Cancel</button> </a>
                                        </div>

                                    </td>
                                   
                                </tr>

                            </table> 
                            
                            <input type="hidden" name="comment_id" value="<?php echo $commentRow['comment_id']; ?>" /> 
                            <input type="hidden" name="qUrl" value="<?php echo $qUrl; ?>" />
                            <input type="hidden" name="fUrl" value="<?php echo $fUrl; ?>" />

                        </form>

                    </div> <!-- form wrapper -->
                    
                </div>
            </div>
        </div> <!-- container -->
        
        <?php echo \com\indigloo\fs\util\Asset::version("/js/fs-bundle.js"); ?>

        <script type="text/javascript">

            $(document).ready(function(){

                $("#form1").validate({
                    errorLabelContainer: $("#form-message"),
                    rules: {
                        name: {required: true } ,
                        email: {required: true } ,
                        price: {required: true } ,
                        quantity: {required: true } ,
                        
                    },
                    messages: {
                        name: {required: " Buyer Name is required" },
                        email: {required: " Buyer Email is required"},
                        price: {required: " Price of item is required"},
                        quantity: {required: " Quantity is required"}

                    }
                });

            });
        
        </script>

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

