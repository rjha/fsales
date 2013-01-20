<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

?>

<!DOCTYPE html>
<html>

    <head>
        <title> New Invoice</title>
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
                        <h3> Zaakpay Tx test </h3>
                    </div>

                    <div class="form-wrapper">
                        
                        <div id="form-message"> </div>
                        <form  id="form1"  name="form1" action="/app/ping/zaakpay-tx.php"  method="POST">
                          
                            <table class="form-table">
                                
                                <tr>
                                    <td> <label>orderId*</label>
                                        <input type="text" name="orderId" maxlength="64" value="" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td> <label>responseCode*</label>
                                        <input type="text" name="responseCode" maxlength="3" value="" />
                                    </td>
                                </tr>

                                <tr>
                                    <td> <label>responseDescription*</label>
                                        <input type="text" name="responseDescription" maxlength="64" value="" />
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td>
                                        <div class="form-actions">
                                            <button class="btn btn-success" type="submit" name="save" value="Save">Test now</button>
                                            
                                        </div>

                                    </td>
                                   
                                </tr>

                            </table> 
                            
                        </form>

                    </div> <!-- form wrapper -->
                    
                </div>
            </div>
        </div> <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

