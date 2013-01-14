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
                        <div class="section">
                            <h3> Timeline comments </h3>
                            IMAGE_1 <br>
                            <p class="comment-text"> Your fans buy by commenting on your post </p>
                        </div>

                         <div class="section">
                            <h3> Comments dashboard </h3>
                            IMAGE_2 <br>
                            <p class="comment-text"> All comments appear on your dashboard </p>
                        </div>

                        <div class="section">
                            <h3> Invoice status </h3>
                            IMAGE_3 <br>
                            <p class="comment-text"> You can update the invoice status </p>
                        </div>


                    </div>
                </div>

            </div><!-- row:1 -->

        </div>   <!-- container -->

        <?php include(APP_WEB_DIR . '/app/inc/footer.inc'); ?>

    </body>
</html>

