<?php
    include ('fs-app.inc');
    include (APP_WEB_DIR.'/app/inc/header.inc');

    

?>

<!DOCTYPE html>
<html>

    <head>
        <title> <?php echo G_APP_TAGLINE ?></title>
        <?php include(APP_WEB_DIR . '/app/inc/meta.inc'); ?>
        <?php echo \com\indigloo\fs\util\Asset::version("/css/fs-bundle.css"); ?>


    </head>

     <body>
        <?php include(APP_WEB_DIR . '/app/inc/toolbar.inc'); ?>
        
        <div class="container mh600">
             <?php include(APP_WEB_DIR . '/app/inc/top/site.inc'); ?>
            <div class="row">
                <div class="span8 offset1">
                    <div class="pt100">
                       <table class="table table-striped">
                          <caption><h3> Pricing </h3> </caption>
                          <thead>
                            <tr>
                              <th>&nbsp;</th>
                              <th>&nbsp;</th>
                            </tr>
                          </thead>
                          <tbody>

                            <tr class="success">
                              <td>1) All Users</td>
                              <td> <strong> 15 Day free Trial </strong> </td>
                            </tr>
                            <tr>
                                <td>2) Small Business</td>
                                <td> 
                                    Rs. 200 per month + Bank transfer(max Rs. 25) <br>
                                    Upto Rs. 2000 in Favsales transactions 
                                 
                                </td>
                            </tr>

                             <tr>
                              <td>3) Standard</td>
                                <td> 
                                   Rs. 200 + 3% + Bank transfer(max Rs. 25) <br>
                                    From Rs. 2000 to Rs. 5,00,000 in Favsales transactions 
                                 
                                </td>
                            </tr>
                             <tr>
                              <td>4) Enterprise</td>
                              <td>  
                                Above Rs. 5,00,000 in Favsales transactions <br>
                                Please contact us at <a href="mailto:support@favsales.com">support@favsales.com</a> for pricing.
                              </td>
                            </tr>


                          </tbody>
                        </table>
                    </div>
                </div>

            </div><!-- row:1 -->

        </div>   <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

