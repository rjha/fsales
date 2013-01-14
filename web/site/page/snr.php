
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
                        <p class="lead">
							Favsales is an enabler of sales for merchants on their Facebook page. 
							The shipping is done directly by the sellers and refund also will 
							be handled directly by the merchants.
                        </p>
                        <dl>
							<dt>What are the shipping charges? </dt>
							<dd>

							Sellers ship directly to the buyer. The sellers can ship free or 
							charge differently for shipping depending on nature of the product. 
							For International orders, the shipping charges may be different 
							for different sellers and the same can be checked with the merchants 
							as indicated on their Facebook page. 

							</dd>
							<dt>What is the estimated delivery time and how will the delivery be done?  </dt>
							<dd>
								The estimated time of delivery should be checked with seller.
								Some sellers may ship internationally as well.
							</dd>
							<dt>What is your refund policy?</dt>
							<dd>
								For refunds, you will have to directly get in touch with sellers. 
								The sellers may decide to refund depending on their policies 
								and procedures.

							</dd>
						</dl>
                    </div>
                </div>

            </div><!-- row:1 -->

        </div>   <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>




